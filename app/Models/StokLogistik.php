<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokLogistik extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'stok_logistiks';

    protected $fillable = [
        'item_logistik_id',
        'jumlah_saat_ini',
        'jumlah_minimum',
    ];

    public function itemLogistik()
    {
        return $this->belongsTo(ItemLogistik::class);
    }

    public function pemasukanLogistiks()
    {
        return $this->hasMany(PemasukanLogistik::class);
    }

    public function pengeluaranLogistiks()
    {
        return $this->hasMany(PengeluaranLogistik::class);
    }

    public function scopeMendesak($query)
    {
        return $query->whereRaw('jumlah_saat_ini <= jumlah_minimum')->where('jumlah_minimum', '>', 0);
    }

    public function getStatusAttribute()
    {
        if ($this->jumlah_minimum <= 0) {
            return 'Aman';
        }
        if ($this->jumlah_saat_ini < ($this->jumlah_minimum * 0.8)) {
            return 'Sangat Mendesak';
        } elseif ($this->jumlah_saat_ini <= $this->jumlah_minimum) {
            return 'Mendesak';
        }
        return 'Aman';
    }
}
