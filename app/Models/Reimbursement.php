<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reimbursement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'nominal', 'jenis_pengeluaran', 'keterangan',
        'status', 'tgl_pengajuan', 'tgl_validasi', 'validated_by', 'bukti_nota',
    ];

    protected $casts = [
        'tgl_pengajuan' => 'date',
        'tgl_validasi' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
