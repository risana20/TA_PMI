<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donasi_makanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donasi_id')->constrained('donasis')->cascadeOnDelete();
            $table->string('nama_makanan')->nullable();
            $table->enum('jenis_makanan', ['Bahan Mentah', 'Siap Saji'])->nullable();
            $table->string('jumlah_makanan')->nullable();
            $table->string('bukti_diterima')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donasi_makanans');
    }
};
