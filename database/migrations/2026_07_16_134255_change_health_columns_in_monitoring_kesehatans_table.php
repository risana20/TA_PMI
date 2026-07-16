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
        Schema::table('monitoring_kesehatans', function (Blueprint $table) {
            $table->smallInteger('frek_napas')->nullable()->change();
            $table->string('tekanan_darah', 10)->nullable()->change();
            $table->decimal('suhu_tubuh', 3, 1)->nullable()->change();
            $table->smallInteger('nadi')->nullable()->change();
            $table->smallInteger('spo2')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitoring_kesehatans', function (Blueprint $table) {
            $table->string('frek_napas')->nullable()->change();
            $table->string('tekanan_darah')->nullable()->change();
            $table->string('suhu_tubuh')->nullable()->change();
            $table->string('nadi')->nullable()->change();
            $table->string('spo2')->nullable()->change();
        });
    }
};
