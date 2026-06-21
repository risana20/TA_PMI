<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\WargaBinaan;

class WargaBinaanTest extends TestCase
{
    use RefreshDatabase;

    protected $roleAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->roleAdmin = Role::firstOrCreate(
            ['name' => 'admin'], 
            ['description' => 'Role Admin']
        );
    }

    public function test_admin_bisa_menambahkan_warga_binaan_baru()
    {
        $admin = User::factory()->create([
            'role_id' => $this->roleAdmin->id,
            'is_active' => true,
        ]);
        
        // Asumsi rute admin butuh verifikasi email sesuai web.php
        $admin->markEmailAsVerified();

        $payload = [
            'nik' => '3201012003900001',
            'nama' => 'Ujang Setiawan',
            'tempat_lahir' => 'Sukabumi',
            'tgl_lahir' => '1990-03-20',
            'alamat' => 'Jl. Raya Pelabuhan Ratu No. 45',
            'jenis_kelamin' => 'L',
            'kategori' => 'ODGJ',
            'status' => 'Aktif',
            'tgl_masuk' => now()->format('Y-m-d'),
            'penanggung_jawab' => 'Asep (Keluarga)',
            'kontak_pj' => '081234567890',
            'catatan' => 'Pasien diantar oleh dinsos',
        ];

        $response = $this->actingAs($admin)
                         ->post(route('admin.warga-binaan.store'), $payload);

        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Data warga binaan berhasil ditambahkan.');
        
        $this->assertDatabaseHas('warga_binaans', [
            'nik' => '3201012003900001',
            'nama' => 'Ujang Setiawan',
            'tempat_lahir' => 'Sukabumi',
            'kategori' => 'ODGJ',
            'status' => 'Aktif',
        ]);
    }
}
