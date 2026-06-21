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

        // Mock gambar sebagai bukti nota
        $fileNota = UploadedFile::fake()->image('nota_pembelian.jpg');

        $payload = [
            'details' => [
                [
                    'nama_kebutuhan' => 'Beras 5kg',
                    'nominal' => 75000,
                    'jenis_logistik_id' => $jenisLogistik->id,
                ],
                [
                    'nama_kebutuhan' => 'Minyak Goreng 2L',
                    'nominal' => 35000,
                    'jenis_logistik_id' => $jenisLogistik->id,
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
            'nama_kebutuhan' => 'Beras 5kg',
            'nominal' => 75000,
        ]);

        $this->assertDatabaseHas('detail_reimbursements', [
            'reimbursement_id' => 1,
            'nama_kebutuhan' => 'Minyak Goreng 2L',
            'nominal' => 35000,
        ]);

        // Pastikan file tersimpan pada storage fake
        $reimbursement = \App\Models\Reimbursement::first();
        Storage::disk('public')->assertExists($reimbursement->bukti_nota);
    }
}
