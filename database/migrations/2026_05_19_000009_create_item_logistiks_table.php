<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_logistiks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_logistik_id')->constrained('jenis_logistiks')->cascadeOnDelete();
            $table->string('nama_item');
            $table->string('satuan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_logistiks');
    }
};
