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
        Schema::table('pemasukan_logistiks', function (Blueprint $table) {
            $table->dropForeign(['stok_logistik_id']);
            $table->unsignedBigInteger('stok_logistik_id')->nullable()->change();
            $table->foreign('stok_logistik_id')->references('id')->on('stok_logistiks')->nullOnDelete();
            
            $table->string('nama_barang')->nullable()->after('donasi_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemasukan_logistiks', function (Blueprint $table) {
            $table->dropColumn('nama_barang');
            $table->dropForeign(['stok_logistik_id']);
            $table->unsignedBigInteger('stok_logistik_id')->nullable(false)->change();
            $table->foreign('stok_logistik_id')->references('id')->on('stok_logistiks')->cascadeOnDelete();
        });
    }
};
