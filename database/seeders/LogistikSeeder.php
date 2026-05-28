<?php

namespace Database\Seeders;

use App\Models\Logistik;
use Illuminate\Database\Seeder;

class LogistikSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['nama' => 'Beras',          'kategori' => 'Makanan', 'stok_saat_ini' => 150, 'stok_minimum' => 50,  'satuan' => 'kg'],
            ['nama' => 'Mie Instan',     'kategori' => 'Makanan', 'stok_saat_ini' => 40,  'stok_minimum' => 50,  'satuan' => 'dus'],
            ['nama' => 'Minyak Goreng',  'kategori' => 'Makanan', 'stok_saat_ini' => 10,  'stok_minimum' => 30,  'satuan' => 'liter'],
            ['nama' => 'Pakaian Layak',  'kategori' => 'Barang',  'stok_saat_ini' => 80,  'stok_minimum' => 30,  'satuan' => 'pcs'],
            ['nama' => 'Selimut',        'kategori' => 'Barang',  'stok_saat_ini' => 20,  'stok_minimum' => 25,  'satuan' => 'buah'],
            ['nama' => 'Sabun Mandi',    'kategori' => 'Barang',  'stok_saat_ini' => 5,   'stok_minimum' => 50,  'satuan' => 'buah'],
        ];

        foreach ($items as $item) {
            Logistik::firstOrCreate(['nama' => $item['nama']], $item);
        }
    }
}
