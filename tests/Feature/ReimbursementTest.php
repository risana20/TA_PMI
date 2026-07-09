<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\JenisLogistik;

class ReimbursementTest extends TestCase
{
    use RefreshDatabase;

    protected $roleAdmin;
    protected $roleSuperadmin;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup Role
        $this->roleAdmin = Role::firstOrCreate(['name' => 'admin'], ['description' => 'Role Admin']);
        $this->roleSuperadmin = Role::firstOrCreate(['name' => 'superadmin'], ['description' => 'Role Superadmin']);
    }

    public function test_admin_bisa_mengajukan_reimbursement()
    {
        // Fake storage agar tidak menyimpan file asli saat testing
        Storage::fake('public');

        // Setup Admin User
        $admin = User::factory()->create([
            'role_id' => $this->roleAdmin->id,
            'is_active' => true,
        ]);
        $admin->markEmailAsVerified();

        // Setup Jenis Logistik untuk direferensikan dalam reimbursement
        $jenisLogistik = JenisLogistik::create([
            'nama_jenis_logistik' => 'Bahan Pokok',
            'deskripsi' => 'Kebutuhan pangan',
        ]);

        // Setup Item Logistik
        $itemBeras = \App\Models\ItemLogistik::create([
            'jenis_logistik_id' => $jenisLogistik->id,
            'nama_item' => 'Beras 5kg',
            'satuan' => 'Pcs',
        ]);

        $itemMinyak = \App\Models\ItemLogistik::create([
            'jenis_logistik_id' => $jenisLogistik->id,
            'nama_item' => 'Minyak Goreng 2L',
            'satuan' => 'Pcs',
        ]);

        // Mock gambar sebagai bukti nota
        $fileNota = UploadedFile::fake()->image('nota_pembelian.jpg');

        $payload = [
            'details' => [
                [
                    'item_logistik_id' => $itemBeras->id,
                    'jumlah' => 1,
                    'nominal' => 75000,
                ],
                [
                    'item_logistik_id' => $itemMinyak->id,
                    'jumlah' => 1,
                    'nominal' => 35000,
                ]
            ],
            'bukti_nota' => $fileNota,
        ];

        // Lakukan request POST pengajuan reimbursement
        $response = $this->actingAs($admin)
            ->post(route('admin.reimbursement.store'), $payload);

        // Pastikan sukses redirect (302) dengan session success
        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Ajuan reimbursement berhasil dikirim.');

        // Assert di tabel pengeluarans header (total 75000 + 35000 = 110000)
        $this->assertDatabaseHas('pengeluarans', [
            'user_id' => $admin->id,
            'status' => 'Tunggu Verifikasi',
            'total' => 110000,
        ]);

        // Karena cuma insert 1 header, kita bisa ambil id = 1 untuk asserts
        $this->assertDatabaseHas('detail_reimbursements', [
            'reimbursement_id' => 1,
            'item_logistik_id' => $itemBeras->id,
            'jumlah' => 1,
            'nominal' => 75000,
        ]);

        $this->assertDatabaseHas('detail_reimbursements', [
            'reimbursement_id' => 1,
            'item_logistik_id' => $itemMinyak->id,
            'jumlah' => 1,
            'nominal' => 35000,
        ]);

        // Pastikan file tersimpan pada storage fake
        $reimbursement = \App\Models\Reimbursement::first();
        Storage::disk('public')->assertExists($reimbursement->bukti_nota);
    }

    public function test_superadmin_cannot_approve_reimbursement_if_insufficient_saldo()
    {
        $superadmin = User::factory()->create([
            'role_id' => $this->roleSuperadmin->id,
            'is_active' => true,
        ]);
        $superadmin->markEmailAsVerified();

        $reimbursement = \App\Models\Reimbursement::create([
            'user_id' => $superadmin->id,
            'tgl_pengajuan' => now()->toDateString(),
            'status' => 'Tunggu Verifikasi',
            'bukti_nota' => 'bukti.png',
            'total' => 50000,
        ]);

        // Kirim request approve (validasi) oleh superadmin
        $response = $this->actingAs($superadmin)
            ->post(route('superadmin.acc-reimbursement.validasi', $reimbursement->id));

        // Harus gagal dan diredirect dengan pesan error di session
        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Persetujuan gagal! Saldo keuangan tidak mencukupi untuk memproses reimbursement ini.');

        // Status di database harus tetap 'Tunggu Verifikasi'
        $this->assertDatabaseHas('pengeluarans', [
            'id' => $reimbursement->id,
            'status' => 'Tunggu Verifikasi',
        ]);
    }

    public function test_superadmin_can_approve_reimbursement_if_sufficient_saldo()
    {
        $superadmin = User::factory()->create([
            'role_id' => $this->roleSuperadmin->id,
            'is_active' => true,
        ]);
        $superadmin->markEmailAsVerified();

        // Buat donasi uang dengan status 'Selesai' untuk mengisi saldo
        $donasi = \App\Models\Donasi::create([
            'user_id' => $superadmin->id,
            'jenis' => 'Uang',
            'nama_donatur' => 'John Doe',
            'status' => 'Selesai',
        ]);

        \App\Models\DonasiUang::create([
            'donasi_id' => $donasi->id,
            'nominal' => 200000,
            'bank_tujuan' => 'Mandiri',
            'bukti_transfer' => 'transfer.png',
        ]);

        $reimbursement = \App\Models\Reimbursement::create([
            'user_id' => $superadmin->id,
            'tgl_pengajuan' => now()->toDateString(),
            'status' => 'Tunggu Verifikasi',
            'bukti_nota' => 'bukti.png',
            'total' => 150000,
        ]);

        // Setup Item & Stok Logistik
        $jenisLogistik = JenisLogistik::create([
            'nama_jenis_logistik' => 'Bahan Pokok',
            'deskripsi' => 'Kebutuhan pangan',
        ]);

        $itemBeras = \App\Models\ItemLogistik::create([
            'jenis_logistik_id' => $jenisLogistik->id,
            'nama_item' => 'Beras 5kg',
            'satuan' => 'Pcs',
        ]);

        $stokBeras = \App\Models\StokLogistik::create([
            'item_logistik_id' => $itemBeras->id,
            'jumlah_saat_ini' => 10,
            'jumlah_minimum' => 5,
        ]);

        // Setup Detail Reimbursement
        \App\Models\DetailReimbursement::create([
            'reimbursement_id' => $reimbursement->id,
            'item_logistik_id' => $itemBeras->id,
            'jumlah' => 5,
            'nominal' => 75000,
        ]);

        // Kirim request approve (validasi) oleh superadmin
        $response = $this->actingAs($superadmin)
            ->post(route('superadmin.acc-reimbursement.validasi', $reimbursement->id));

        // Harus berhasil redirect (302) dengan session success
        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Reimbursement berhasil disetujui.');

        // Status di database harus berubah menjadi 'Disetujui'
        $this->assertDatabaseHas('pengeluarans', [
            'id' => $reimbursement->id,
            'status' => 'Disetujui',
        ]);

        // Pastikan jumlah stok logistik bertambah (10 + 5 = 15)
        $this->assertEquals(15, $stokBeras->fresh()->jumlah_saat_ini);

        // Pastikan data riwayat pemasukan otomatis tercatat
        $this->assertDatabaseHas('pemasukan_logistiks', [
            'stok_logistik_id' => $stokBeras->id,
            'user_id'          => $superadmin->id,
            'jumlah'           => 5,
            'keterangan'       => 'pembelian',
            'kondisi'          => 'Baru',
        ]);

        $pemasukan = \App\Models\PemasukanLogistik::where('stok_logistik_id', $stokBeras->id)->first();
        $this->assertNotNull($pemasukan);
        $this->assertEquals(now()->toDateString(), $pemasukan->tanggal->toDateString());
    }
}
