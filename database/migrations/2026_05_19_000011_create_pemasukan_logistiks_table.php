<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
    }

    public function down(): void
    {
        Schema::dropIfExists('pemasukan_logistiks');
    }
};
