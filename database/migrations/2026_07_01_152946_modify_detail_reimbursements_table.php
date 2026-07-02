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

            
            $table->dropForeign(['jenis_logistik_id']);

            $table->dropColumn([
                'nama_kebutuhan',
                'jenis_logistik_id'
            ]);


            $table->foreignId('item_logistik_id')
                  ->after('reimbursement_id')
                  ->constrained('item_logistiks')
                  ->restrictOnDelete();

            $table->unsignedInteger('jumlah')
                  ->after('item_logistik_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('detail_reimbursements', function (Blueprint $table) {

            $table->dropForeign(['item_logistik_id']);

            $table->dropColumn([
                'item_logistik_id',
                'jumlah'
            ]);

            $table->string('nama_kebutuhan');

            $table->foreignId('jenis_logistik_id')
                  ->constrained('jenis_logistiks')
                  ->restrictOnDelete();
        });
    }
};
