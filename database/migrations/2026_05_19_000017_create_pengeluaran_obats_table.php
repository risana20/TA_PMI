<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengeluaran_obats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stok_obat_id')->constrained('stok_obats')->cascadeOnDelete();
            $table->integer('jumlah');
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengeluaran_obats');
    }
};
