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
            $table->foreignId('pengaju')
                  ->nullable()
                  ->after('user_id')
                  ->constrained('users')
                  ->nullOnDelete();
        });

        Schema::table('pengeluaran_logistiks', function (Blueprint $table) {
            $table->foreignId('pengaju')
                  ->nullable()
                  ->after('user_id')
                  ->constrained('users')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemasukan_logistiks', function (Blueprint $table) {
            $table->dropForeign(['pengaju']);
            $table->dropColumn('pengaju');
        });

        Schema::table('pengeluaran_logistiks', function (Blueprint $table) {
            $table->dropForeign(['pengaju']);
            $table->dropColumn('pengaju');
        });
    }
};
