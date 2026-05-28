<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokObat extends Model
{
    use HasFactory;

    protected $fillable = [
        'warga_binaan_id',
        'nama_obat',
        'aturan_minum',
        'stok',
        'satuan',
        'bentuk_obat',
        'jumlah_awal',
        'sisa',
        'tgl_mulai',
        'tgl_update',
        'status',
        'asal_obat',
        'keterangan',
    ];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_update' => 'date',
    ];

    public function wargaBinaan()
    {
        return $this->belongsTo(WargaBinaan::class);
    }

    public function pengeluaranObats()
    {
        return $this->hasMany(PengeluaranObat::class);
    }
}
