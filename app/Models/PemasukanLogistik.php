<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemasukanLogistik extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'pemasukan_logistiks';

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'tgl_penyerahan' => 'date',
        ];
    }

    protected $fillable = [
        'stok_logistik_id',
        'user_id',
        'pengaju',
        'donasi_id',
        'nama_barang',
        'jumlah',
        'satuan',
        'tanggal',
        'keterangan',
        'kondisi',
        'metode_penyerahan',
        'tgl_penyerahan',
        'jam_penyerahan',
        'bukti_diterima',
        'status',
    ];

    public function stokLogistik()
    {
        return $this->belongsTo(StokLogistik::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pengajuUser()
    {
        return $this->belongsTo(User::class, 'pengaju');
    }

    public function donasi()
    {
        return $this->belongsTo(Donasi::class);
    }
}
