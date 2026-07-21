<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use App\Models\StokLogistik;
use App\Models\WargaBinaan;
use App\Models\Donasi;

class BerandaController extends Controller
{
    public function index()
    {
        $artikelTerbaru    = Artikel::published()->latest()->limit(3)->get();
        $kebutuhanMendesak = StokLogistik::with('itemLogistik')->mendesak()->limit(4)->get();
        $totalWarga        = WargaBinaan::where('status', 'Aktif')->count();
        $totalODGJ = WargaBinaan::whereIn('kategori', ['ODGJ','Lansia ODGJ'])->count();
        $totalLansia = WargaBinaan::whereIn('kategori', ['Lansia','Lansia ODGJ'])->count();

        // Fetch verified donors (status: Selesai)
        $donaturTerverifikasi = Donasi::with('user')
            ->where('status', 'Selesai')
            ->latest()
            ->get()
            ->unique('nama_donatur');

        // If list is small, repeat it to ensure seamless scrolling marquee
        if ($donaturTerverifikasi->count() > 0 && $donaturTerverifikasi->count() < 6) {
            $donaturTerverifikasi = $donaturTerverifikasi->concat($donaturTerverifikasi)->concat($donaturTerverifikasi);
        }

        return view('pages.public.beranda', compact('artikelTerbaru', 'kebutuhanMendesak', 'totalWarga', 'donaturTerverifikasi', 'totalODGJ', 'totalLansia'));
    }
}
