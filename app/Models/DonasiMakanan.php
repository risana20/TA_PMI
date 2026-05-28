<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonasiMakanan extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'donasi_makanans';

    protected $fillable = [
        'donasi_id',
        'nama_makanan',
        'jenis_makanan',
        'jumlah_makanan',
        'bukti_diterima',
        'metode_penyerahan',
        'tgl_penyerahan',
        'jam_penyerahan',
    ];

    public function donasi()
    {
        return $this->belongsTo(Donasi::class);
    }
}
