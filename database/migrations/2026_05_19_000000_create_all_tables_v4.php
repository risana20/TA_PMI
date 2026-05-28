<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warga_binaans', function (Blueprint $table) {
            $table->id();
            $table->char('nik', 16)->unique();
            $table->string('nama', 100);
            $table->string('tempat_lahir', 50);
            $table->date('tgl_lahir');
            $table->text('alamat');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->enum('kategori', ['ODGJ', 'Lansia']);
            $table->enum('status', ['Aktif', 'Selesai Pembinaan', 'Meninggal', 'Kabur'])->default('Aktif');
            $table->date('tgl_masuk');
            $table->string('no_bpjs', 15)->nullable();
            $table->text('catatan')->nullable();
            $table->string('penanggung_jawab', 100)->nullable();
            $table->string('kontak_pj', 20)->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });

        Schema::create('artikels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->string('kategori')->nullable();
            $table->longText('konten');
            $table->string('gambar')->nullable();
            $table->enum('status', ['PUBLISHED', 'DRAFT'])->default('DRAFT');
            $table->date('tgl_terbit')->nullable();
            $table->timestamps();
        });

        Schema::create('donasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('jenis', ['Uang', 'Barang', 'Makanan']);
            $table->string('nama_donatur');
            $table->enum('status', ['Tunggu Verifikasi', 'Donasi Ditolak', 'Menunggu Pengiriman', 'Menunggu Donasi Dijemput Petugas', 'Selesai'])->default('Tunggu Verifikasi');
            $table->text('alasan_penolakan')->nullable();
            $table->timestamps();
        });

        Schema::create('donasi_uangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donasi_id')->constrained('donasis')->cascadeOnDelete();
            $table->bigInteger('nominal')->nullable();
            $table->string('bank_tujuan')->nullable();
            $table->string('bukti_transfer')->nullable();
        });

        Schema::create('donasi_makanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donasi_id')->constrained('donasis')->cascadeOnDelete();
            $table->string('nama_makanan')->nullable();
            $table->enum('jenis_makanan', ['Bahan Mentah', 'Siap Saji'])->nullable();
            $table->string('jumlah_makanan')->nullable();
            $table->string('bukti_diterima')->nullable();
        });

        Schema::create('reimbursements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->bigInteger('nominal');
            $table->enum('jenis_pengeluaran', ['Makanan', 'Barang', 'Obat']);
            $table->text('keterangan')->nullable();
            $table->enum('status', ['Tunggu Verifikasi', 'Ditolak', 'Disetujui'])->default('Tunggu Verifikasi');
            $table->date('tgl_pengajuan');
            $table->date('tgl_validasi')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('bukti_nota')->nullable();
            $table->timestamps();
        });

        Schema::create('kunjungans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama_pengunjung');
            $table->string('no_hp', 20);
            $table->text('tujuan');
            $table->string('instansi')->nullable();
            $table->date('tgl_kunjungan');
            $table->time('jam');
            $table->string('surat_pengajuan')->nullable();
            $table->enum('status', ['PROSES', 'DISETUJUI', 'DITOLAK'])->default('PROSES');
            $table->text('alasan_tolak')->nullable();
            $table->timestamps();
        });

        Schema::create('jenis_logistiks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jenis_logistik');
        });

        Schema::create('item_logistiks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_logistik_id')->constrained('jenis_logistiks')->cascadeOnDelete();
            $table->string('nama_item');
            $table->string('satuan');
        });

        Schema::create('stok_logistiks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_logistik_id')->constrained('item_logistiks')->cascadeOnDelete();
            $table->integer('jumlah_saat_ini')->default(0);
            $table->integer('jumlah_minimum')->default(0);
        });

        Schema::create('pemasukan_logistiks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stok_logistik_id')->constrained('stok_logistiks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('donasi_id')->nullable()->constrained('donasis')->nullOnDelete();
            $table->integer('jumlah');
            $table->string('satuan')->nullable();
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->enum('kondisi', ['Baru', 'Bekas Layak'])->nullable();
            $table->enum('metode_penyerahan', ['Antar Sendiri', 'Dijemput Petugas'])->nullable();
            $table->date('tgl_penyerahan')->nullable();
            $table->time('jam_penyerahan')->nullable();
            $table->string('bukti_diterima')->nullable();
            $table->enum('status', ['Tunggu Verifikasi', 'Donasi Ditolak', 'Menunggu Pengiriman', 'Menunggu Donasi Dijemput Petugas', 'Selesai'])->nullable();
        });

        Schema::create('pengeluaran_logistiks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stok_logistik_id')->constrained('stok_logistiks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('warga_binaan_id')->constrained('warga_binaans')->cascadeOnDelete();
            $table->integer('jumlah');
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
        });

        Schema::create('monitoring_kesehatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warga_binaan_id')->constrained('warga_binaans')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('frek_napas')->nullable();
            $table->string('tekanan_darah')->nullable();
            $table->string('suhu_tubuh')->nullable();
            $table->string('nadi')->nullable();
            $table->string('spo2')->nullable();
            $table->decimal('berat_badan', 5, 1)->nullable();
            $table->decimal('tinggi_badan', 5, 1)->nullable();
            $table->text('keluhan')->nullable();
            $table->text('tindakan')->nullable();
            $table->text('catatan')->nullable();
            $table->string('petugas')->nullable();
            $table->timestamps();
        });

        Schema::create('pemeriksaan_rsjs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warga_binaan_id')->constrained('warga_binaans')->cascadeOnDelete();
            $table->date('tgl_kontrol');
            $table->text('kondisi')->nullable();
            $table->text('gejala')->nullable();
            $table->text('obat')->nullable();
            $table->text('catatan')->nullable();
            $table->date('kontrol_berikutnya')->nullable();
            $table->timestamps();
        });

        Schema::create('riwayat_penyakits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warga_binaan_id')->constrained('warga_binaans')->cascadeOnDelete();
            $table->foreignId('monitoring_kesehatan_id')->nullable()->constrained('monitoring_kesehatans')->nullOnDelete();
            $table->string('nama_penyakit');
            $table->string('status')->default('Aktif');
            $table->date('tanggal')->nullable();
            $table->timestamps();
        });

        Schema::create('stok_obats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warga_binaan_id')->constrained('warga_binaans')->cascadeOnDelete();
            $table->string('nama_obat');
            $table->integer('stok')->default(0);
            $table->string('satuan');
            $table->string('bentuk_obat')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('aturan_minum')->nullable();
            $table->integer('jumlah_awal')->nullable();
            $table->integer('sisa')->nullable();
            $table->date('tgl_mulai')->nullable();
            $table->date('tgl_update')->nullable();
            $table->string('status')->default('AKTIF');
            $table->string('asal_obat')->default('OBAT_PERIKSA');
            $table->timestamps();
        });

        Schema::create('pengeluaran_obats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stok_obat_id')->constrained('stok_obats')->cascadeOnDelete();
            $table->integer('jumlah');
            $table->date('tanggal');
            $table->timestamps();
        });

        Schema::create('surat_rujukan_odgjs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warga_binaan_id')->constrained('warga_binaans')->cascadeOnDelete();
            $table->date('tanggal_terbit');
            $table->date('tanggal_berakhir');
            $table->string('file_surat')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_rujukan_odgjs');
        Schema::dropIfExists('pengeluaran_obats');
        Schema::dropIfExists('stok_obats');
        Schema::dropIfExists('riwayat_penyakits');
        Schema::dropIfExists('pemeriksaan_rsjs');
        Schema::dropIfExists('monitoring_kesehatans');
        Schema::dropIfExists('pengeluaran_logistiks');
        Schema::dropIfExists('pemasukan_logistiks');
        Schema::dropIfExists('stok_logistiks');
        Schema::dropIfExists('item_logistiks');
        Schema::dropIfExists('jenis_logistiks');
        Schema::dropIfExists('kunjungans');
        Schema::dropIfExists('reimbursements');
        Schema::dropIfExists('donasi_makanans');
        Schema::dropIfExists('donasi_uangs');
        Schema::dropIfExists('donasis');
        Schema::dropIfExists('artikels');
        Schema::dropIfExists('warga_binaans');
    }
};
