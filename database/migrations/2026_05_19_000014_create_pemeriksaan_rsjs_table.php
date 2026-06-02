<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
    }

    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_rsjs');
    }
};
