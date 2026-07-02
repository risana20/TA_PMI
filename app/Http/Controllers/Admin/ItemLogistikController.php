<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ItemLogistik;
use App\Models\StokLogistik;

class ItemLogistikController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama_item' => 'required|string|max:255|unique:item_logistiks,nama_item',
            'satuan' => 'required|string|max:50',
            'jenis_logistik_id' => 'required|exists:jenis_logistiks,id',
        ]);

        $item = ItemLogistik::create([
            'nama_item' => $request->nama_item,
            'satuan' => $request->satuan,
            'jenis_logistik_id' => $request->jenis_logistik_id,
        ]);

        StokLogistik::create([
            'item_logistik_id' => $item->id,
            'jumlah_saat_ini' => 0,
            'jumlah_minimum' => 0,
        ]);

        return response()->json([
            'success' => true,
            'item' => $item
        ]);
    }
}