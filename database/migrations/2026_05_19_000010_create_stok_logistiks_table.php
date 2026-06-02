<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_logistiks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_logistik_id')->constrained('item_logistiks')->cascadeOnDelete();
            $table->integer('jumlah_saat_ini')->default(0);
            $table->integer('jumlah_minimum')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_logistiks');
    }
};
