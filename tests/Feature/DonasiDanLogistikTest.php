<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\JenisLogistik;
use App\Models\WargaBinaan;
use App\Models\StokObat;

class DonasiDanLogistikTest extends TestCase
{
    use RefreshDatabase;

    protected $roleDonatur;
    protected $roleAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        // Siapkan role untuk user testing
        $this->roleDonatur = Role::firstOrCreate(['name' => 'donatur'], ['description' => 'Role Donatur']);
        $this->roleAdmin = Role::firstOrCreate(['name' => 'admin'], ['description' => 'Role Admin']);
    }

    public function test_donatur_mengajukan_donasi_barang()
    {
        $user = User::factory()->create([
            'role_id' => $this->roleDonatur->id,
            'is_active' => true,
        ]);
        $user->markEmailAsVerified();

        $response = $this->actingAs($user)->post(route('donasi.store'), [
            'jenis' => 'Barang',
            'nama_donatur' => 'Hamba Allah',
            'nama_barang' => 'Kursi Roda',
            'jumlah_barang' => 1,
            'satuan' => 'Pcs',
            'kondisi' => 'Baru',
            'metode_penyerahan' => 'Antar Sendiri',
            'tgl_penyerahan' => now()->format('Y-m-d'),
            'jam_penyerahan' => '10:00',
        ]);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('donasis', [
            'user_id' => $user->id,
            'jenis' => 'Barang',
            'nama_donatur' => 'Hamba Allah',
        ]);

        $this->assertDatabaseHas('pemasukan_logistiks', [
            'nama_barang' => 'Kursi Roda',
            'jumlah' => 1,
            'kondisi' => 'Baru',
            'status' => 'Tunggu Verifikasi',
        ]);
    }

    public function test_admin_tambah_stok_logistik()
    {
        $admin = User::factory()->create([
            'role_id' => $this->roleAdmin->id,
            'is_active' => true,
        ]);
        $admin->markEmailAsVerified();

        $jenisLogistik = JenisLogistik::create([
            'nama_jenis_logistik' => 'Obat',
            'deskripsi' => 'Farmasi'
        ]);

        $response = $this->actingAs($admin)->post(route('admin.logistik.store'), [
            'jenis_logistik_id' => $jenisLogistik->id,
            'nama_item' => 'Paracetamol Tablet',
            'satuan' => 'Strip',
            'jumlah_saat_ini' => 100,
            'jumlah_minimum' => 20,
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('item_logistiks', [
            'jenis_logistik_id' => $jenisLogistik->id,
            'nama_item' => 'Paracetamol Tablet',
            'satuan' => 'Strip',
        ]);

        $this->assertDatabaseHas('stok_logistiks', [
            'jumlah_saat_ini' => 100,
            'jumlah_minimum' => 20,
        ]);
    }

    public function test_admin_penambahan_obat_di_warga_binaan()
    {
        $admin = User::factory()->create([
            'role_id' => $this->roleAdmin->id,
            'is_active' => true,
        ]);
        $admin->markEmailAsVerified();

        $wargaBinaan = WargaBinaan::create([
            'nik' => '1234567812345678',
            'nama' => 'Budi Santoso',
            'tempat_lahir' => 'Jakarta',
            'tgl_lahir' => '1980-01-01',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Kebon Jeruk No 1',
            'kategori' => 'ODGJ',
            'status' => 'Aktif',
            'tgl_masuk' => now()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($admin)->post(route('admin.monitoring.storeObat', $wargaBinaan->id), [
            'nama_obat' => 'Amoxicillin',
            'aturan_minum' => '3x1',
            'bentuk_obat' => 'Kapsul',
            'jumlah_awal' => 30,
            'tgl_mulai' => now()->format('Y-m-d'),
            'asal_obat' => 'OBAT_PERIKSA',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('stok_obats', [
            'warga_binaan_id' => $wargaBinaan->id,
            'nama_obat' => 'Amoxicillin',
            'jumlah_awal' => 30,
            'sisa' => 30,
            'satuan' => 'Kapsul',
            'asal_obat' => 'OBAT_PERIKSA',
        ]);
    }

    public function test_admin_pencatatan_minum_obat()
    {
        $admin = User::factory()->create([
            'role_id' => $this->roleAdmin->id,
            'is_active' => true,
        ]);
        $admin->markEmailAsVerified();

        $wargaBinaan = WargaBinaan::create([
            'nik' => '8765432187654321',
            'nama' => 'Siti Nurhaliza',
            'tempat_lahir' => 'Bandung',
            'tgl_lahir' => '1995-05-05',
            'jenis_kelamin' => 'P',
            'alamat' => 'Jl. Asia Afrika No 2',
            'kategori' => 'ODGJ',
            'status' => 'Aktif',
            'tgl_masuk' => now()->format('Y-m-d'),
        ]);

        $stokObat = StokObat::create([
            'warga_binaan_id' => $wargaBinaan->id,
            'nama_obat' => 'Vitamin C',
            'aturan_minum' => '1x1',
            'satuan' => 'Tablet',
            'jumlah_awal' => 10,
            'sisa' => 10,
            'tgl_mulai' => now()->format('Y-m-d'),
            'tgl_update' => now()->format('Y-m-d'),
            'status' => 'AKTIF',
            'asal_obat' => 'OBAT_PERIKSA',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.monitoring.storePengeluaranObat', ['wargaBinaan' => $wargaBinaan->id, 'stokObat' => $stokObat->id]), [
            'jumlah' => 1,
            'tanggal' => now()->format('Y-m-d'),
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('pengeluaran_obats', [
            'stok_obat_id' => $stokObat->id,
            'jumlah' => 1,
        ]);

        $this->assertDatabaseHas('stok_obats', [
            'id' => $stokObat->id,
            'sisa' => 9,
        ]);
    }

    public function test_admin_bisa_memfilter_donasi_berdasarkan_status_donasi_dan_status_logistik()
    {
        $admin = User::factory()->create([
            'role_id' => $this->roleAdmin->id,
            'is_active' => true,
        ]);
        $admin->markEmailAsVerified();

        $donatur = User::factory()->create([
            'role_id' => $this->roleDonatur->id,
        ]);

        // Donasi 1: Barang, Tunggu Verifikasi, Belum Ditambahkan ke stok
        $donasi1 = \App\Models\Donasi::create([
            'user_id' => $donatur->id,
            'jenis' => 'Barang',
            'nama_donatur' => 'Donatur A',
            'status' => 'Tunggu Verifikasi',
        ]);
        $pemasukan1 = \App\Models\PemasukanLogistik::create([
            'user_id' => $donatur->id,
            'donasi_id' => $donasi1->id,
            'nama_barang' => 'Kursi Roda A',
            'jumlah' => 1,
            'tanggal' => now()->format('Y-m-d'),
            'kondisi' => 'Baru',
            'metode_penyerahan' => 'Antar Sendiri',
            'status' => 'Tunggu Verifikasi',
        ]);

        // Donasi 2: Barang, Selesai, Sudah Ditambahkan ke stok (stok_logistik_id set)
        $donasi2 = \App\Models\Donasi::create([
            'user_id' => $donatur->id,
            'jenis' => 'Barang',
            'nama_donatur' => 'Donatur B',
            'status' => 'Selesai',
        ]);

        $jenisLogistik = JenisLogistik::create([
            'nama_jenis_logistik' => 'Barang',
            'deskripsi' => 'Alat'
        ]);
        $itemLogistik = \App\Models\ItemLogistik::create([
            'jenis_logistik_id' => $jenisLogistik->id,
            'nama_item' => 'Kursi Roda B',
            'satuan' => 'Pcs',
        ]);
        $stokLogistik = \App\Models\StokLogistik::create([
            'item_logistik_id' => $itemLogistik->id,
            'jumlah_saat_ini' => 10,
            'jumlah_minimum' => 2,
        ]);

        $pemasukan2 = \App\Models\PemasukanLogistik::create([
            'stok_logistik_id' => $stokLogistik->id,
            'user_id' => $donatur->id,
            'donasi_id' => $donasi2->id,
            'nama_barang' => 'Kursi Roda B',
            'jumlah' => 1,
            'tanggal' => now()->format('Y-m-d'),
            'kondisi' => 'Baru',
            'metode_penyerahan' => 'Antar Sendiri',
            'status' => 'Selesai',
        ]);

        // Test Filter 1: status_donasi = Selesai
        $response = $this->actingAs($admin)->get(route('admin.donasi.index', [
            'tab' => 'Barang',
            'status_donasi' => 'Selesai',
        ]));
        $response->assertStatus(200);
        $response->assertSee('Donatur B');
        $response->assertDontSee('Donatur A');

        // Test Filter 2: status_logistik = Sudah
        $response = $this->actingAs($admin)->get(route('admin.donasi.index', [
            'tab' => 'Barang',
            'status_logistik' => 'Sudah',
        ]));
        $response->assertStatus(200);
        $response->assertSee('Donatur B');
        $response->assertDontSee('Donatur A');

        // Test Filter 3: status_logistik = Belum
        $response = $this->actingAs($admin)->get(route('admin.donasi.index', [
            'tab' => 'Barang',
            'status_logistik' => 'Belum',
        ]));
        $response->assertStatus(200);
        $response->assertSee('Donatur A');
        $response->assertDontSee('Donatur B');
    }

    public function test_admin_bisa_mengedit_stok_minimum_logistik()
    {
        $admin = User::factory()->create([
            'role_id' => $this->roleAdmin->id,
            'is_active' => true,
        ]);
        $admin->markEmailAsVerified();

        $jenisLogistik = JenisLogistik::create([
            'nama_jenis_logistik' => 'Makanan',
            'deskripsi' => 'Konsumsi'
        ]);

        $itemLogistik = \App\Models\ItemLogistik::create([
            'jenis_logistik_id' => $jenisLogistik->id,
            'nama_item' => 'Beras Pandan Wangi',
            'satuan' => 'Kg',
        ]);

        $stokLogistik = \App\Models\StokLogistik::create([
            'item_logistik_id' => $itemLogistik->id,
            'jumlah_saat_ini' => 50,
            'jumlah_minimum' => 10,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.logistik.update-stok', $stokLogistik->id), [
            'jumlah_minimum' => 15,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Stok minimum berhasil diperbarui.');

        $this->assertDatabaseHas('stok_logistiks', [
            'id' => $stokLogistik->id,
            'jumlah_minimum' => 15,
        ]);
    }
}
