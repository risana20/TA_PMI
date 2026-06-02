<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
    }

    public function down(): void
    {
        Schema::dropIfExists('monitoring_kesehatans');
    }
};
