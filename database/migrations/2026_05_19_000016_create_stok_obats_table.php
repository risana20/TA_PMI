<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_obats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warga_binaan_id')->constrained('warga_binaans')->cascadeOnDelete();
            $table->string('nama_obat');
            $table->integer('stok')->default(0);
            $table->string('satuan');
            $table->string('bentuk_obat')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('aturan_minum')->nullable();
            $table->integer('jumlah_awal')->nullable();
            $table->integer('sisa')->nullable();
            $table->date('tgl_mulai')->nullable();
            $table->date('tgl_update')->nullable();
            $table->string('status')->default('AKTIF');
            $table->string('asal_obat')->default('OBAT_PERIKSA');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_obats');
    }
};
