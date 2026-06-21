<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donasi;
use App\Models\DonasiUang;
use App\Models\Reimbursement;
use App\Models\DetailReimbursement;
use App\Models\Kunjungan;
use App\Models\StokLogistik;
use App\Models\WargaBinaan;

class DashboardController extends Controller
{
    public function index()
    {
        $totalWarga   = WargaBinaan::where('status', 'Aktif')->count();
        
        $pemasukan = DonasiUang::whereHas('donasi', function($q) {
            $q->where('status', 'Selesai');
        })->sum('nominal');
        $pengeluaran = Reimbursement::where('status', 'Disetujui')->sum('total');
        $totalSaldo   = $pemasukan - $pengeluaran;

        $kunjunganBulanIni = Kunjungan::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $kebutuhanMendesak = StokLogistik::mendesak()->count();

        $aktivitasDonasi   = Donasi::latest()->limit(5)->get();
        $aktivitasKunjungan = Kunjungan::latest()->limit(5)->get();

        return view('pages.admin.dashboard', compact(
            'totalWarga', 'totalSaldo', 'kunjunganBulanIni', 'kebutuhanMendesak',
            'aktivitasDonasi', 'aktivitasKunjungan'
        ));
    }
}
