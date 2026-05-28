<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reimbursement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReimbursementController extends Controller
{
    public function index()
    {
        $reimbursements = Reimbursement::where('user_id', Auth::id())->latest()->paginate(10);
        return view('pages.admin.reimbursement.index', compact('reimbursements'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nominal'           => 'required|integer|min:1',
            'jenis_pengeluaran' => 'required|in:Makanan,Barang,Obat',
            'keterangan'        => 'nullable|string',
            'bukti_nota'        => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('bukti_nota')) {
            $data['bukti_nota'] = $request->file('bukti_nota')->store('reimbursement', 'public');
        }

        $data['user_id']      = Auth::id();
        $data['tgl_pengajuan'] = now()->toDateString();
        $data['status']       = 'Tunggu Verifikasi';

        Reimbursement::create($data);
        return back()->with('success', 'Ajuan reimbursement berhasil dikirim.');
    }
}
