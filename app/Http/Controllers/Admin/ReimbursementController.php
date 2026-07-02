<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reimbursement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DetailReimbursement;
use App\Models\ItemLogistik;
use App\Models\JenisLogistik;

class ReimbursementController extends Controller
{
    public function index()
    {
       
        $reimbursements = Reimbursement::with('detailReimbursements.itemLogistik')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        $itemLogistiks = ItemLogistik::with('jenisLogistik')->get();
        $satuans = ItemLogistik::select('satuan')
            ->distinct()
            ->orderBy('satuan')
            ->pluck('satuan');
        $jenisLogistiks = JenisLogistik::all();
        
        return view('pages.admin.reimbursement.index', compact('reimbursements', 'itemLogistiks', 'jenisLogistiks','satuans'));
         
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
        // dd('validasi lolos');
       

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
        

        // dd($reimbursement);

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
}
