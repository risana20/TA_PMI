<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reimbursement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DetailReimbursement;
use App\Models\JenisLogistik;

class ReimbursementController extends Controller
{
    public function index()
    {
        
        $reimbursements = Reimbursement::with('detailReimbursements.jenisLogistik')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        $jenisLogistiks = JenisLogistik::all();

        return view('pages.admin.reimbursement.index', compact('reimbursements', 'jenisLogistiks'));
         
    }

    public function store(Request $request)
    {
       
        $request->validate([
            'details' => 'required|array|min:1',
            'details.*.nama_kebutuhan' => 'required|string',
            'details.*.nominal' => 'required|integer|min:1',
            'details.*.jenis_logistik_id' => 'required|exists:jenis_logistiks,id',
            'bukti_nota' => 'required|nullable|image|max:10000',
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
                'nama_kebutuhan' => $item['nama_kebutuhan'],
                'nominal' => $item['nominal'],
                'jenis_logistik_id' => $item['jenis_logistik_id'],
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
