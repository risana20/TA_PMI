<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
    }

    public function down(): void
    {
        Schema::dropIfExists('kunjungans');
    }
};
