<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Donasi;
use App\Models\Kunjungan;
use Illuminate\Support\Facades\Auth;

class CekStatusController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $donasiUang    = Donasi::where('user_id', $userId)->where('jenis', 'Uang')->latest()->get();
        $donasiBarang  = Donasi::where('user_id', $userId)->where('jenis', 'Barang')->latest()->get();
        $donasiMakanan = Donasi::where('user_id', $userId)->where('jenis', 'Makanan')->latest()->get();
        $kunjungan     = Kunjungan::where('user_id', $userId)->latest()->get();

        // Fetch all approved visits from today onwards
        $jadwalDisetujui = Kunjungan::where('status', 'DISETUJUI')
                            ->whereDate('tgl_kunjungan', '>=', today())
                            ->orderBy('tgl_kunjungan')
                            ->get();

        return view('pages.user.cek-status', compact(
            'donasiUang', 'donasiBarang', 'donasiMakanan', 'kunjungan', 'jadwalDisetujui'
        ));
    }
}
