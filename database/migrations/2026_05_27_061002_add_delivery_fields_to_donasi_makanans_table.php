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
        Schema::table('donasi_makanans', function (Blueprint $table) {
            $table->enum('metode_penyerahan', ['Antar Sendiri', 'Dijemput Petugas'])->nullable()->after('jumlah_makanan');
            $table->date('tgl_penyerahan')->nullable()->after('metode_penyerahan');
            $table->time('jam_penyerahan')->nullable()->after('tgl_penyerahan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donasi_makanans', function (Blueprint $table) {
            $table->dropColumn(['metode_penyerahan', 'tgl_penyerahan', 'jam_penyerahan']);
        });
    }
};
