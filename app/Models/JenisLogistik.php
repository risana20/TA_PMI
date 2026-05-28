<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisLogistik extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'jenis_logistiks';

    protected $fillable = [
        'nama_jenis_logistik',
    ];

    public function itemLogistiks()
    {
        return $this->hasMany(ItemLogistik::class);
    }
}
