<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pemeriksaan_rsjs', function (Blueprint $table) {
            $table->foreignId('surat_rujukan_odgj_id')
                ->nullable()
                ->after('warga_binaan_id')
                ->constrained('surat_rujukan_odgjs')
                ->nullOnDelete();
        });

        // Auto-link existing records based on date and resident ID
        $pemeriksaans = DB::table('pemeriksaan_rsjs')->get();
        foreach ($pemeriksaans as $p) {
            $rujukan = DB::table('surat_rujukan_odgjs')
                ->where('warga_binaan_id', $p->warga_binaan_id)
                ->where('tanggal_terbit', '<=', $p->tgl_kontrol)
                ->where('tanggal_berakhir', '>=', $p->tgl_kontrol)
                ->first();

            if ($rujukan) {
                DB::table('pemeriksaan_rsjs')
                    ->where('id', $p->id)
                    ->update(['surat_rujukan_odgj_id' => $rujukan->id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemeriksaan_rsjs', function (Blueprint $table) {
            $table->dropForeign(['surat_rujukan_odgj_id']);
            $table->dropColumn('surat_rujukan_odgj_id');
        });
    }
};
