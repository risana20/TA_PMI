<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengeluaranObat extends Model
{
    use HasFactory;

    protected $fillable = [
        'stok_obat_id',
        'jumlah',
        'tanggal',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function stokObat()
    {
        return $this->belongsTo(StokObat::class);
    }
}
