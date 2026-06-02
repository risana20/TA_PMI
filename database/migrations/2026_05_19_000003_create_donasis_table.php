<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('jenis', ['Uang', 'Barang', 'Makanan']);
            $table->string('nama_donatur');
            $table->enum('status', ['Tunggu Verifikasi', 'Donasi Ditolak', 'Menunggu Pengiriman', 'Menunggu Donasi Dijemput Petugas', 'Selesai'])->default('Tunggu Verifikasi');
            $table->text('alasan_penolakan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donasis');
    }
};
