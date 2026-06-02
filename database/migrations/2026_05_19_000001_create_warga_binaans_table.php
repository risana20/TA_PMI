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
    }

    public function down(): void
    {
        Schema::dropIfExists('warga_binaans');
    }
};
