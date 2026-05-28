<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonasiUang;
use App\Models\Reimbursement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeuanganController extends Controller
{
    public function index()
    {
        // Pemasukan: Donasi Uang yang statusnya 'Selesai'
        $pemasukans = DonasiUang::whereHas('donasi', function($q) {
            $q->where('status', 'Selesai');
        })->with('donasi.user')->orderByDesc('id')->paginate(10, ['*'], 'pemasukan_page');

        // Pengeluaran: Reimbursement yang statusnya 'Disetujui'
        $pengeluarans = Reimbursement::where('status', 'Disetujui')
            ->with('user')
            ->latest()
            ->paginate(10, ['*'], 'pengeluaran_page');

        $totalPemasukan = DonasiUang::whereHas('donasi', function($q) {
            $q->where('status', 'Selesai');
        })->sum('nominal');

        $totalPengeluaran = Reimbursement::where('status', 'Disetujui')->sum('nominal');
        
        $saldo = $totalPemasukan - $totalPengeluaran;

        // Data grafik distribusi pengeluaran per kategori
        $distribusiPengeluaran = Reimbursement::where('status', 'Disetujui')
            ->selectRaw('jenis_pengeluaran as kategori, SUM(nominal) as total')
            ->groupBy('jenis_pengeluaran')
            ->get();

        return view('pages.admin.keuangan.index', compact(
            'pemasukans', 'pengeluarans', 'totalPemasukan', 'totalPengeluaran', 'saldo', 'distribusiPengeluaran'
        ));
    }

    // Pemasukan only comes from Donasi forms now, so manual storePemasukan is deprecated.
    // KeuanganController only views finances or handles reimbursement approvals.
    
    // Store pengeluaran manually (can be mapped to creating a pre-approved Reimbursement if needed)
    public function storePengeluaran(Request $request)
    {
        $data = $request->validate([
            'tanggal'    => 'required|date',
            'nominal'    => 'required|integer|min:1',
            'jenis_pengeluaran' => 'required|in:Makanan,Barang,Obat',
            'keterangan' => 'nullable|string',
            'bukti_nota' => 'nullable|image|max:2048',
        ]);

        $totalPemasukan = DonasiUang::whereHas('donasi', function($q) {
            $q->where('status', 'Selesai');
        })->sum('nominal');

        $totalPengeluaran = Reimbursement::where('status', 'Disetujui')->sum('nominal');
        $saldo = $totalPemasukan - $totalPengeluaran;

        if ($data['nominal'] > $saldo) {
            return back()
                ->withErrors(['nominal' => 'Saldo tidak cukup untuk melakukan pengeluaran'])
                ->withInput();
        }

        if ($request->hasFile('bukti_nota')) {
            $data['bukti_nota'] = $request->file('bukti_nota')->store('keuangan', 'public');
        }

        Reimbursement::create([
            'user_id' => Auth::id(),
            'nominal' => $data['nominal'],
            'jenis_pengeluaran' => $data['jenis_pengeluaran'],
            'keterangan' => $data['keterangan'] ?? null,
            'status' => 'Disetujui', // Directly approved if stored from here
            'tgl_pengajuan' => $data['tanggal'],
            'tgl_validasi' => now(),
            'validated_by' => Auth::id(),
            'bukti_nota' => $data['bukti_nota'] ?? null,
        ]);

        return back()->with('success', 'Pengeluaran berhasil dicatat.');
    }
}
