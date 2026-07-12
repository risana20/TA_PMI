<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reimbursement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DetailReimbursement;
use App\Models\ItemLogistik;
use App\Models\JenisLogistik;
use App\Exports\ReimbursementExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;


class ReimbursementController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status_filter');

        $query = Reimbursement::with('detailReimbursements.itemLogistik')
            ->where('user_id', Auth::id());

        if ($search) {
            $query->where(function ($q) use ($search) {

                $q->where('tgl_pengajuan', 'like', "%{$search}%")
                ->orWhere('total', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%")

                ->orWhereHas('detailReimbursements', function ($detail) use ($search) {
                    $detail->where('jumlah', 'like', "%{$search}%")
                            ->orWhere('nominal', 'like', "%{$search}%")
                            ->orWhereHas('itemLogistik', function ($item) use ($search) {
                                $item->where('nama_item', 'like', "%{$search}%")
                                    ->orWhere('satuan', 'like', "%{$search}%");
                            });
                });

            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $reimbursements = $query->latest()
            ->paginate(10)
            ->withQueryString();

        $itemLogistiks = ItemLogistik::with('jenisLogistik')->get();

        $satuans = ItemLogistik::select('satuan')
            ->distinct()
            ->orderBy('satuan')
            ->pluck('satuan');

        $jenisLogistiks = JenisLogistik::all();

        return view('pages.admin.reimbursement.index',compact('reimbursements','itemLogistiks','jenisLogistiks','satuans','search','status'));
    }

    public function store(Request $request)
    {
       
        $request->validate([
            'details' => 'required|array|min:1',
            'details.*.item_logistik_id' => 'required|exists:item_logistiks,id',
            'details.*.jumlah' => 'required|integer|min:1',
            'details.*.nominal' => 'required|numeric|min:1',
            'bukti_nota' => 'required|image|max:10000',
        ]);

        // upload file
        $bukti = null;
        if ($request->hasFile('bukti_nota')) {
            $bukti = $request->file('bukti_nota')->store('reimbursement', 'public');
        }

        // buat reimbursement header dulu
        $reimbursement = Reimbursement::create([
            'user_id' => Auth::id(),
            'tgl_pengajuan' => now()->toDateString(),
            'status' => 'Tunggu Verifikasi',
            'bukti_nota' => $bukti,
            'total' => 0,
        ]);

        $total = 0;

        // simpan detail
        
        foreach ($request->details as $item) {
            DetailReimbursement::create([
                'reimbursement_id' => $reimbursement->id,
                'item_logistik_id' => $item ['item_logistik_id'],
                'jumlah' => $item['jumlah'],
                'nominal' => $item['nominal'],
            ]);

            $total += $item['nominal'];
        }

        // update total
        $reimbursement->update([
            'total' => $total
        ]);

        return back()->with('success', 'Ajuan reimbursement berhasil dikirim.');
    }
    public function exportExcel(Request $request)
    {
        return Excel::download(
            new ReimbursementExport(),
            'Laporan_Ajuan_Reimbursement.xlsx'
        );
    }
    public function exportPdf(Request $request)

    {
        $reimbursements = Reimbursement::with([
            'detailReimbursements.itemLogistik',
            'user', 'validator'
        ])
        ->where('user_id', Auth::id())
        ->latest()
        ->get();

        $pdf = Pdf::loadView(
            'pages.admin.reimbursement.export_pdf',
            compact('reimbursements')
        );

        return $pdf->download('Laporan_Ajuan_Reimbursement.pdf');
    }

}
