<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            WargaBinaanSeeder::class,
            // LogistikSeeder::class, // (Dinonaktifkan sementara karena tabel Logistik lama telah dihapus dan diganti)
        ]);
    }
}
