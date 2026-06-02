<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_logistiks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jenis_logistik');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_logistiks');
    }
};
