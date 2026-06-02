<?php

namespace Database\Seeders;

use App\Models\JenisLogistik;
use App\Models\ItemLogistik;
use App\Models\StokLogistik;
use Illuminate\Database\Seeder;

class LogistikSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Categories (Jenis Logistik)
        $makanan = JenisLogistik::firstOrCreate(['nama_jenis_logistik' => 'Makanan']);
        $barang  = JenisLogistik::firstOrCreate(['nama_jenis_logistik' => 'Barang']);
        $obat    = JenisLogistik::firstOrCreate(['nama_jenis_logistik' => 'Obat']);

        // 2. Seed Items and Stock
        $items = [
            // Makanan
            [
                'jenis_id' => $makanan->id,
                'nama' => 'Beras',
                'satuan' => 'kg',
                'stok_saat_ini' => 150,
                'stok_minimum' => 50,
            ],
            [
                'jenis_id' => $makanan->id,
                'nama' => 'Mie Instan',
                'satuan' => 'dus',
                'stok_saat_ini' => 40,
                'stok_minimum' => 50,
            ],
            [
                'jenis_id' => $makanan->id,
                'nama' => 'Minyak Goreng',
                'satuan' => 'liter',
                'stok_saat_ini' => 10,
                'stok_minimum' => 30,
            ],
            // Barang
            [
                'jenis_id' => $barang->id,
                'nama' => 'Pakaian Layak Pakai',
                'satuan' => 'pcs',
                'stok_saat_ini' => 80,
                'stok_minimum' => 30,
            ],
            [
                'jenis_id' => $barang->id,
                'nama' => 'Selimut',
                'satuan' => 'pcs',
                'stok_saat_ini' => 20,
                'stok_minimum' => 25,
            ],
            [
                'jenis_id' => $barang->id,
                'nama' => 'Sabun Mandi',
                'satuan' => 'pcs',
                'stok_saat_ini' => 5,
                'stok_minimum' => 50,
            ],
            // Obat
            [
                'jenis_id' => $obat->id,
                'nama' => 'Paracetamol 500mg',
                'satuan' => 'tablet',
                'stok_saat_ini' => 200,
                'stok_minimum' => 50,
            ],
            [
                'jenis_id' => $obat->id,
                'nama' => 'Amoxicillin 500mg',
                'satuan' => 'tablet',
                'stok_saat_ini' => 100,
                'stok_minimum' => 30,
            ],
        ];

        foreach ($items as $item) {
            $itemLogistik = ItemLogistik::firstOrCreate(
                [
                    'jenis_logistik_id' => $item['jenis_id'],
                    'nama_item'         => $item['nama'],
                ],
                [
                    'satuan'            => $item['satuan'],
                ]
            );

            StokLogistik::firstOrCreate(
                ['item_logistik_id' => $itemLogistik->id],
                [
                    'jumlah_saat_ini' => $item['stok_saat_ini'],
                    'jumlah_minimum'  => $item['stok_minimum'],
                ]
            );
        }
    }
}
