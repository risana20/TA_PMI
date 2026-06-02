<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengeluaran_logistiks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stok_logistik_id')->constrained('stok_logistiks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('warga_binaan_id')->constrained('warga_binaans')->cascadeOnDelete();
            $table->integer('jumlah');
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengeluaran_logistiks');
    }
};
