<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\StokLogistik;

class KebutuhanMendesakController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $baseQuery = StokLogistik::with('itemLogistik.jenisLogistik')
            ->whereHas('itemLogistik.jenisLogistik', function ($q) {
                $q->whereIn('nama_jenis_logistik', ['Makanan', 'Barang']);
            })
            ->mendesak();

        if ($request->get('kategori')) {
            $baseQuery->whereHas('itemLogistik.jenisLogistik', function ($q) use ($request) {
                $q->where('nama_jenis_logistik', $request->get('kategori'));
            });
        }

        // Clone base query to get total counts before pagination
        $cloneSangatMendesak = clone $baseQuery;
        $totalSangatMendesak = $cloneSangatMendesak->whereRaw('jumlah_saat_ini < (jumlah_minimum * 0.8)')->count();

        $cloneMendesak = clone $baseQuery;
        $totalMendesak = $cloneMendesak->whereRaw('jumlah_saat_ini >= (jumlah_minimum * 0.8)')->count();

        $items = $baseQuery->paginate(12)->withQueryString();
        return view('pages.public.kebutuhan-mendesak', compact('items', 'totalSangatMendesak', 'totalMendesak'));
    }
}
