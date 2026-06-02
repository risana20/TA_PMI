<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donasi_uangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donasi_id')->constrained('donasis')->cascadeOnDelete();
            $table->bigInteger('nominal')->nullable();
            $table->string('bank_tujuan')->nullable();
            $table->string('bukti_transfer')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donasi_uangs');
    }
};
