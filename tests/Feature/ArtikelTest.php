<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Artikel;

class ArtikelTest extends TestCase
{
    use RefreshDatabase;

    protected $roleAdmin;
    protected $roleUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup Roles
        $this->roleAdmin = Role::firstOrCreate(['name' => 'admin'], ['description' => 'Role Admin']);
        $this->roleUser = Role::firstOrCreate(['name' => 'user'], ['description' => 'Role User']);
    }

    /**
     * Test guest cannot access admin article pages or actions.
     */
    public function test_guest_tidak_bisa_mengakses_halaman_dan_aksi_artikel_admin()
    {
        $adminUser = User::factory()->create(['role_id' => $this->roleAdmin->id]);

        // Indeks
        $response = $this->get(route('admin.artikel.index'));
        $response->assertRedirect(route('login'));

        // Store
        $response = $this->post(route('admin.artikel.store'), []);
        $response->assertRedirect(route('login'));

        // Buat artikel awal untuk update & delete
        $artikel = Artikel::create([
            'user_id' => $adminUser->id,
            'judul' => 'Artikel Contoh',
            'konten' => 'Konten artikel contoh.',
            'status' => 'PUBLISHED',
        ]);

        // Update
        $response = $this->put(route('admin.artikel.update', $artikel->id), []);
        $response->assertRedirect(route('login'));

        // Delete
        $response = $this->delete(route('admin.artikel.destroy', $artikel->id));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test non-admin user cannot access admin article pages or actions.
     */
    public function test_non_admin_tidak_bisa_mengakses_halaman_dan_aksi_artikel_admin()
    {
        $user = User::factory()->create([
            'role_id' => $this->roleUser->id,
            'is_active' => true,
        ]);
        $user->markEmailAsVerified();

        // Indeks
        $response = $this->actingAs($user)->get(route('admin.artikel.index'));
        $response->assertStatus(403);

        // Store
        $response = $this->actingAs($user)->post(route('admin.artikel.store'), []);
        $response->assertStatus(403);

        // Buat artikel awal
        $admin = User::factory()->create(['role_id' => $this->roleAdmin->id]);
        $artikel = Artikel::create([
            'user_id' => $admin->id,
            'judul' => 'Artikel Contoh Admin',
            'konten' => 'Konten artikel contoh admin.',
            'status' => 'PUBLISHED',
        ]);

        // Update
        $response = $this->actingAs($user)->put(route('admin.artikel.update', $artikel->id), []);
        $response->assertStatus(403);

        // Delete
        $response = $this->actingAs($user)->delete(route('admin.artikel.destroy', $artikel->id));
        $response->assertStatus(403);
    }

    /**
     * Test admin can view admin article index page.
     */
    public function test_admin_bisa_melihat_daftar_artikel()
    {
        $admin = User::factory()->create([
            'role_id' => $this->roleAdmin->id,
            'is_active' => true,
        ]);
        $admin->markEmailAsVerified();

        $response = $this->actingAs($admin)->get(route('admin.artikel.index'));
        $response->assertStatus(200);
        $response->assertViewIs('pages.admin.artikel.index');
    }

    /**
     * Test admin can upload a new article with an image.
     */
    public function test_admin_bisa_mengunggah_artikel_baru_dengan_gambar()
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role_id' => $this->roleAdmin->id,
            'is_active' => true,
        ]);
        $admin->markEmailAsVerified();

        $fileGambar = UploadedFile::fake()->image('artikel_pmi.jpg');

        $payload = [
            'judul' => 'Kegiatan Sosial PMI Daerah',
            'kategori' => 'Sosial',
            'konten' => 'PMI mengadakan kegiatan donor darah massal di alun-alun kota.',
            'gambar' => $fileGambar,
            'status' => 'PUBLISHED',
        ];

        // Lakukan request POST store
        $response = $this->actingAs($admin)
                         ->from(route('admin.artikel.index'))
                         ->post(route('admin.artikel.store'), $payload);

        $response->assertRedirect(route('admin.artikel.index'));
        $response->assertSessionHas('success', 'Artikel berhasil disimpan.');

        // Cek database
        $this->assertDatabaseHas('artikels', [
            'user_id' => $admin->id,
            'judul' => 'Kegiatan Sosial PMI Daerah',
            'kategori' => 'Sosial',
            'konten' => 'PMI mengadakan kegiatan donor darah massal di alun-alun kota.',
            'status' => 'PUBLISHED',
        ]);

        // Cek apakah slug dibuat otomatis dan tgl_terbit di-cast dengan benar
        $artikel = Artikel::first();
        $this->assertNotNull($artikel->slug);
        $this->assertStringContainsString('kegiatan-sosial-pmi-daerah', $artikel->slug);
        $this->assertEquals(now()->toDateString(), $artikel->tgl_terbit->toDateString());

        // Cek file tersimpan pada storage fake
        Storage::disk('public')->assertExists($artikel->gambar);
    }

    /**
     * Test admin can upload a new article draft with an image.
     */
    public function test_admin_bisa_mengunggah_artikel_draft_dengan_gambar()
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role_id' => $this->roleAdmin->id,
            'is_active' => true,
        ]);
        $admin->markEmailAsVerified();

        $fileGambar = UploadedFile::fake()->image('draft_pmi.jpg');

        $payload = [
            'judul' => 'Rencana Kerja PMI Bulan Depan',
            'kategori' => 'Internal',
            'konten' => 'Berikut adalah draf rencana kerja internal PMI.',
            'gambar' => $fileGambar,
            'status' => 'DRAFT',
        ];

        $response = $this->actingAs($admin)
                         ->from(route('admin.artikel.index'))
                         ->post(route('admin.artikel.store'), $payload);

        $response->assertRedirect(route('admin.artikel.index'));
        $response->assertSessionHas('success', 'Artikel berhasil disimpan.');

        // Cek database
        $this->assertDatabaseHas('artikels', [
            'user_id' => $admin->id,
            'judul' => 'Rencana Kerja PMI Bulan Depan',
            'kategori' => 'Internal',
            'konten' => 'Berikut adalah draf rencana kerja internal PMI.',
            'status' => 'DRAFT',
            'tgl_terbit' => null, // DRAFT tidak memiliki tgl_terbit saat store
        ]);

        // Cek file gambar tersimpan
        $artikel = Artikel::first();
        Storage::disk('public')->assertExists($artikel->gambar);
    }

    /**
     * Test admin fails to upload article due to validation error (missing fields including gambar).
     */
    public function test_admin_gagal_mengunggah_artikel_jika_validasi_tidak_valid()
    {
        $admin = User::factory()->create([
            'role_id' => $this->roleAdmin->id,
            'is_active' => true,
        ]);
        $admin->markEmailAsVerified();

        // Judul kosong, konten kosong, gambar kosong, status tidak valid
        $payload = [
            'judul' => '',
            'konten' => '',
            'status' => 'INVALID_STATUS',
            'gambar' => null,
        ];

        $response = $this->actingAs($admin)
                         ->from(route('admin.artikel.index'))
                         ->post(route('admin.artikel.store'), $payload);

        $response->assertRedirect(route('admin.artikel.index'));
        $response->assertSessionHasErrors(['judul', 'konten', 'status', 'gambar']);
        $this->assertDatabaseCount('artikels', 0);
    }

    /**
     * Test admin can update an existing article and replace its image.
     */
    public function test_admin_bisa_mengedit_artikel_dan_memperbarui_gambar()
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role_id' => $this->roleAdmin->id,
            'is_active' => true,
        ]);
        $admin->markEmailAsVerified();

        // Buat artikel awal dengan gambar lama
        $fileGambarLama = UploadedFile::fake()->image('gambar_lama.png')->store('artikel', 'public');
        $artikel = Artikel::create([
            'user_id' => $admin->id,
            'judul' => 'Judul Awal',
            'kategori' => 'Lama',
            'konten' => 'Konten awal artikel.',
            'gambar' => $fileGambarLama,
            'status' => 'DRAFT',
            'tgl_terbit' => null,
        ]);

        Storage::disk('public')->assertExists($fileGambarLama);

        // Payload update dengan gambar baru dan ganti status ke PUBLISHED
        $fileGambarBaru = UploadedFile::fake()->image('gambar_baru.png');
        $payload = [
            'judul' => 'Judul Diperbarui',
            'kategori' => 'Baru',
            'konten' => 'Konten artikel yang telah diperbarui.',
            'gambar' => $fileGambarBaru,
            'status' => 'PUBLISHED',
        ];

        $response = $this->actingAs($admin)
                         ->from(route('admin.artikel.index'))
                         ->put(route('admin.artikel.update', $artikel->id), $payload);

        $response->assertRedirect(route('admin.artikel.index'));
        $response->assertSessionHas('success', 'Artikel berhasil diperbarui.');

        // Verifikasi database terupdate
        $this->assertDatabaseHas('artikels', [
            'id' => $artikel->id,
            'judul' => 'Judul Diperbarui',
            'kategori' => 'Baru',
            'konten' => 'Konten artikel yang telah diperbarui.',
            'status' => 'PUBLISHED',
        ]);

        // Verifikasi gambar lama dihapus
        Storage::disk('public')->assertMissing($fileGambarLama);

        // Verifikasi gambar baru tersimpan dan tgl_terbit di-cast dengan benar
        $artikelBaru = $artikel->fresh();
        Storage::disk('public')->assertExists($artikelBaru->gambar);
        $this->assertEquals(now()->toDateString(), $artikelBaru->tgl_terbit->toDateString());
    }

    /**
     * Test admin can delete an article and its image is deleted.
     */
    public function test_admin_bisa_menghapus_artikel()
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role_id' => $this->roleAdmin->id,
            'is_active' => true,
        ]);
        $admin->markEmailAsVerified();

        // Buat artikel awal dengan gambar
        $fileGambar = UploadedFile::fake()->image('gambar_artikel.png')->store('artikel', 'public');
        $artikel = Artikel::create([
            'user_id' => $admin->id,
            'judul' => 'Artikel Mau Dihapus',
            'konten' => 'Konten artikel mau dihapus.',
            'gambar' => $fileGambar,
            'status' => 'PUBLISHED',
        ]);

        Storage::disk('public')->assertExists($fileGambar);

        // Hapus artikel
        $response = $this->actingAs($admin)
                         ->from(route('admin.artikel.index'))
                         ->delete(route('admin.artikel.destroy', $artikel->id));

        $response->assertRedirect(route('admin.artikel.index'));
        $response->assertSessionHas('success', 'Artikel berhasil dihapus.');

        // Pastikan terhapus di database
        $this->assertDatabaseMissing('artikels', [
            'id' => $artikel->id,
        ]);

        // Pastikan file gambar dihapus dari storage
        Storage::disk('public')->assertMissing($fileGambar);
    }
}

