<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
    }
};
