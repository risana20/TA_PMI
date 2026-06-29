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

    public function test_admin_bisa_menambahkan_warga_binaan_lansia_odgj()
    {
        $admin = User::factory()->create([
            'role_id' => $this->roleAdmin->id,
            'is_active' => true,
        ]);
        $admin->markEmailAsVerified();

        $payload = [
            'nik' => '3201012003900002',
            'nama' => 'Mbah Sumo',
            'tempat_lahir' => 'Solo',
            'tgl_lahir' => '1955-08-15',
            'alamat' => 'Slamet Riyadi Solo',
            'jenis_kelamin' => 'L',
            'kategori' => 'Lansia ODGJ',
            'status' => 'Aktif',
            'tgl_masuk' => now()->format('Y-m-d'),
        ];

        $response = $this->actingAs($admin)
                         ->post(route('admin.warga-binaan.store'), $payload);

        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Data warga binaan berhasil ditambahkan.');
        
        $this->assertDatabaseHas('warga_binaans', [
            'nik' => '3201012003900002',
            'nama' => 'Mbah Sumo',
            'kategori' => 'Lansia ODGJ',
            'status' => 'Aktif',
        ]);
    }

    public function test_warga_binaan_odgj_umur_60_ke_atas_otomatis_kategori_lansia_odgj()
    {
        $admin = User::factory()->create([
            'role_id' => $this->roleAdmin->id,
            'is_active' => true,
        ]);
        $admin->markEmailAsVerified();

        // Umur 60 tahun (lahir tahun 1966 jika sekarang 2026)
        $tglLahir = now()->subYears(60)->format('Y-m-d');

        $payload = [
            'nik' => '3201012003900003',
            'nama' => 'Mbah Ujang',
            'tempat_lahir' => 'Sukabumi',
            'tgl_lahir' => $tglLahir,
            'alamat' => 'Jl. Raya Pelabuhan Ratu No. 45',
            'jenis_kelamin' => 'L',
            'kategori' => 'ODGJ', // Diinput ODGJ tapi umur >= 60
            'status' => 'Aktif',
            'tgl_masuk' => now()->format('Y-m-d'),
        ];

        $response = $this->actingAs($admin)
                         ->post(route('admin.warga-binaan.store'), $payload);

        $response->assertStatus(302);
        
        // Di database harus otomatis tersimpan sebagai Lansia ODGJ
        $this->assertDatabaseHas('warga_binaans', [
            'nik' => '3201012003900003',
            'kategori' => 'Lansia ODGJ',
        ]);
    }

    public function test_tambah_warga_binaan_lansia_odgj_harus_umur_60_ke_atas()
    {
        $admin = User::factory()->create([
            'role_id' => $this->roleAdmin->id,
            'is_active' => true,
        ]);
        $admin->markEmailAsVerified();

        // Umur kurang dari 60 tahun (lahir 59 tahun yang lalu)
        $tglLahir = now()->subYears(59)->format('Y-m-d');

        $payload = [
            'nik' => '3201012003900004',
            'nama' => 'Pak Bambang',
            'tempat_lahir' => 'Sukabumi',
            'tgl_lahir' => $tglLahir,
            'alamat' => 'Jl. Raya Pelabuhan Ratu No. 45',
            'jenis_kelamin' => 'L',
            'kategori' => 'Lansia ODGJ', // Diinput Lansia ODGJ tapi umur < 60
            'status' => 'Aktif',
            'tgl_masuk' => now()->format('Y-m-d'),
        ];

        $response = $this->actingAs($admin)
                         ->post(route('admin.warga-binaan.store'), $payload);

        // Harus gagal validasi
        $response->assertSessionHasErrors('tgl_lahir');
        
        // Tidak boleh tersimpan di database
        $this->assertDatabaseMissing('warga_binaans', [
            'nik' => '3201012003900004',
        ]);
    }
}
