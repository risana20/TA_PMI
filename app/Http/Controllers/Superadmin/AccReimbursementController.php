<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Reimbursement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StokLogistik;

class AccReimbursementController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query  = Reimbursement::with(['user', 'detailReimbursements.itemLogistik'])->latest();

        if ($search) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $reimbursements = $query->paginate(10)->withQueryString();

        // Hitung saldo keuangan saat ini
        $totalPemasukan = \App\Models\DonasiUang::whereHas('donasi', function($q) {
            $q->where('status', 'Selesai');
        })->sum('nominal');

        $totalPengeluaran = Reimbursement::where('status', 'Disetujui')->sum('total');
        $saldo = $totalPemasukan - $totalPengeluaran;

        return view('pages.superadmin.acc-reimbursement.index', compact('reimbursements', 'search', 'saldo'));
    }

    public function validasi(Reimbursement $reimbursement)
    {
        if ($reimbursement->status !== 'Tunggu Verifikasi') {
            return back()->with('error', 'Data sudah diproses.');
        }

        // Hitung saldo keuangan saat ini
        $totalPemasukan = \App\Models\DonasiUang::whereHas('donasi', function($q) {
            $q->where('status', 'Selesai');
        })->sum('nominal');

        $totalPengeluaran = Reimbursement::where('status', 'Disetujui')->sum('total');
        $saldo = $totalPemasukan - $totalPengeluaran;

        if ($reimbursement->total > $saldo) {
            return back()->with('error', 'Persetujuan gagal! Saldo keuangan tidak mencukupi untuk memproses reimbursement ini.');
        }

        $reimbursement->update([
            'status'       => 'Disetujui',
            'tgl_validasi' => now(),
            'validated_by' => Auth::id(),
        ]);
        foreach ($reimbursement->detailReimbursements as $detail) {

            $stok = StokLogistik::where(
                'item_logistik_id',
                $detail->item_logistik_id
            )->first();

            if ($stok) {
                $stok->increment('jumlah_saat_ini', $detail->jumlah);
            }
        }

        return back()->with('success', 'Reimbursement berhasil disetujui.');
    }

    

    public function batalkan(Request $request,Reimbursement $reimbursement)
    {   
        // dd($request->all());
        if ($reimbursement->status !== 'Tunggu Verifikasi') {
            return back()->with('error', 'Data sudah diproses.');
        }

        $reimbursement->update([
            'status'       => 'Ditolak',
            'tgl_validasi' => now(),
            'validated_by' => Auth::id(),
            'alasan_tolak' => $request->alasan_tolak,
        ]);

        return back()->with('success', 'Reimbursement berhasil ditolak.');
    }
}
