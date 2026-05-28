<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonasiUang extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'donasi_uangs';

    protected $fillable = [
        'donasi_id',
        'nominal',
        'bank_tujuan',
        'bukti_transfer',
    ];

    public function donasi()
    {
        return $this->belongsTo(Donasi::class);
    }
}
