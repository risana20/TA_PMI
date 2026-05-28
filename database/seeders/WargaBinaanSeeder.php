<?php

namespace Database\Seeders;

use App\Models\WargaBinaan;
use Illuminate\Database\Seeder;

class WargaBinaanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nik' => '3372010101900001', 'nama' => 'Budi Santoso',
                'tempat_lahir' => 'Surakarta', 'tgl_lahir' => '1990-01-15',
                'alamat' => 'Jl. Veteran No. 10, Surakarta', 'jenis_kelamin' => 'L',
                'kategori' => 'ODGJ', 'status' => 'Aktif', 'tgl_masuk' => '2022-03-10',
                'penanggung_jawab' => 'Dinas Sosial Surakarta',
            ],
            [
                'nik' => '3372010202850002', 'nama' => 'Siti Rahayu',
                'tempat_lahir' => 'Semarang', 'tgl_lahir' => '1985-02-20',
                'alamat' => 'Jl. Merdeka No. 5, Surakarta', 'jenis_kelamin' => 'P',
                'kategori' => 'ODGJ', 'status' => 'Aktif', 'tgl_masuk' => '2023-01-15',
                'no_bpjs' => '0001234567890',
            ],
            [
                'nik' => '3372010303500003', 'nama' => 'Mbah Joko',
                'tempat_lahir' => 'Klaten', 'tgl_lahir' => '1950-03-05',
                'alamat' => 'Jl. Pahlawan No. 20, Surakarta', 'jenis_kelamin' => 'L',
                'kategori' => 'Lansia', 'status' => 'Aktif', 'tgl_masuk' => '2021-06-01',
                'no_bpjs' => '0009876543210', 'penanggung_jawab' => 'Keluarga',
            ],
            [
                'nik' => '3372010404480004', 'nama' => 'Mbah Sarem',
                'tempat_lahir' => 'Boyolali', 'tgl_lahir' => '1948-04-12',
                'alamat' => 'Jl. Diponegoro No. 3, Surakarta', 'jenis_kelamin' => 'P',
                'kategori' => 'Lansia', 'status' => 'Aktif', 'tgl_masuk' => '2020-09-20',
            ],
            [
                'nik' => '3372010505880005', 'nama' => 'Ahmad Fauzi',
                'tempat_lahir' => 'Surakarta', 'tgl_lahir' => '1988-05-30',
                'alamat' => 'Jl. Gatot Subroto No. 8, Surakarta', 'jenis_kelamin' => 'L',
                'kategori' => 'ODGJ', 'status' => 'Selesai Pembinaan', 'tgl_masuk' => '2021-02-10',
            ],
        ];

        foreach ($data as $item) {
            WargaBinaan::firstOrCreate(['nik' => $item['nik']], $item);
        }
    }
}
