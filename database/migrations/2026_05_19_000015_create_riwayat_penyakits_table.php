<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_penyakits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warga_binaan_id')->constrained('warga_binaans')->cascadeOnDelete();
            $table->foreignId('monitoring_kesehatan_id')->nullable()->constrained('monitoring_kesehatans')->nullOnDelete();
            $table->string('nama_penyakit');
            $table->string('status')->default('Aktif');
            $table->date('tanggal')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_penyakits');
    }
};
