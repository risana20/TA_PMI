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
        'warga_binaan_id',
        'jumlah',
        'tanggal',
        'keterangan',
    ];

    public function stokLogistik()
    {
        return $this->belongsTo(StokLogistik::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wargaBinaan()
    {
        return $this->belongsTo(WargaBinaan::class);
    }
}
