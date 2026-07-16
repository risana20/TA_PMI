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
        Schema::table('detail_reimbursements', function (Blueprint $table) {
            $table->unsignedInteger('nominal')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_reimbursements', function (Blueprint $table) {
            $table->decimal('nominal', 15, 2)->change();
        });
    }
};
