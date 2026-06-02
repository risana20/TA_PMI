<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reimbursements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->bigInteger('nominal');
            $table->enum('jenis_pengeluaran', ['Makanan', 'Barang', 'Obat']);
            $table->text('keterangan')->nullable();
            $table->enum('status', ['Tunggu Verifikasi', 'Ditolak', 'Disetujui'])->default('Tunggu Verifikasi');
            $table->date('tgl_pengajuan');
            $table->date('tgl_validasi')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('bukti_nota')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reimbursements');
    }
};
