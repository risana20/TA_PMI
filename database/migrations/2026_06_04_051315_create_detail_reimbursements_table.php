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
        Schema::create('detail_reimbursements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reimbursement_id')
                  ->constrained('reimbursements')
                  ->cascadeOnDelete();

            $table->string('nama_kebutuhan');

            $table->decimal('nominal', 15, 2);

            $table->foreignId('jenis_logistik_id')
                  ->constrained('jenis_logistiks')
                  ->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_reimbursements');
    }
};
