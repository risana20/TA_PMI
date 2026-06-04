<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@griyapmi.id'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password'),
                'phone'    => '081234567890',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $superadmin->assignRole('superadmin');

        $admin = User::firstOrCreate(
            ['email' => 'admin@griyapmi.id'],
            [
                'name'     => 'Admin Griya',
                'password' => Hash::make('password'),
                'phone'    => '081234567891',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        $user = User::firstOrCreate(
            ['email' => 'user@griyapmi.id'],
            [
                'name'     => 'Donatur User',
                'password' => Hash::make('password'),
                'phone'    => '081234567892',
                'address'  => 'Jl. Contoh No. 1, Surakarta',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $user->assignRole('user');
    }
}
