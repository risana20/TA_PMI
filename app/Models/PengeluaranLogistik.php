<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengeluaranLogistik extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'pengeluaran_logistiks';

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    protected $fillable = [
        'stok_logistik_id',
        'user_id',
        'pengaju',
        'warga_binaan_id',
        'jumlah',
        'tanggal',
        'keterangan',
        'aturan_minum',
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

    public function wargaBinaan()
    {
        return $this->belongsTo(WargaBinaan::class);
    }
}
