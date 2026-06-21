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
            $table->dropForeign(['reimbursement_id']);

            $table->foreign('reimbursement_id')
                ->references('id')
                ->on('pengeluarans')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_reimbursements', function (Blueprint $table) {
            $table->dropForeign(['reimbursement_id']);

            $table->foreign('reimbursement_id')
                ->references('id')
                ->on('reimbursements')
                ->onDelete('cascade');
        });
    }
};
