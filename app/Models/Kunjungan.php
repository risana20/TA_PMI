<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kunjungan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'nama_pengunjung', 'no_hp',
         'tujuan','instansi', 'tgl_kunjungan', 'jam',
         'surat_pengajuan', 'status', 'alasan_tolak', 
    ];

    protected $casts = [
        'tgl_kunjungan' =>  'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
