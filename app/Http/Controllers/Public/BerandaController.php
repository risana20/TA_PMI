<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use App\Models\StokLogistik;
use App\Models\WargaBinaan;

class BerandaController extends Controller
{
    public function index()
    {
        $artikelTerbaru    = Artikel::published()->latest()->limit(3)->get();
        $kebutuhanMendesak = StokLogistik::with('itemLogistik')->mendesak()->limit(4)->get();
        $totalWarga        = WargaBinaan::where('status', 'Aktif')->count();

        return view('pages.public.beranda', compact('artikelTerbaru', 'kebutuhanMendesak', 'totalWarga'));
    }
}
