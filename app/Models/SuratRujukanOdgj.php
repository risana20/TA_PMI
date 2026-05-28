<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SuratRujukanOdgj extends Model
{
    use HasFactory;

    protected $table = 'surat_rujukan_odgjs';

    protected $fillable = [
        'warga_binaan_id',
        'tanggal_terbit',
        'tanggal_berakhir',
        'file_surat',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
        'tanggal_berakhir' => 'date',
    ];

    public function wargaBinaan()
    {
        return $this->belongsTo(WargaBinaan::class);
    }

    public function pemeriksaanRsjs()
    {
        return $this->hasMany(PemeriksaanRsj::class);
    }

    /**
     * Return status string based on expiration date.
     */
    public function getStatusAttribute()
    {
        return $this->tanggal_berakhir && $this->tanggal_berakhir->isPast()
            ? 'Kedaluwarsa'
            : 'Aktif';
    }
}
