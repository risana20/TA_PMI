<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('warga_binaans', function (Blueprint $table) {
            $table->string('kategori', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warga_binaans', function (Blueprint $table) {
            $table->enum('kategori', ['ODGJ', 'Lansia'])->change();
        });
    }
};
