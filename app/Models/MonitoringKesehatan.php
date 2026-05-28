<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringKesehatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'warga_binaan_id', 'tanggal',
        'frek_napas', 'tekanan_darah', 'suhu_tubuh', 'nadi', 'spo2',
        'berat_badan', 'tinggi_badan', 'keluhan', 'tindakan',
        'catatan', 'petugas', 'riwayat_penyakit',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function wargaBinaan()
    {
        return $this->belongsTo(WargaBinaan::class);
    }

    public function riwayatPenyakits()
    {
        return $this->hasMany(RiwayatPenyakit::class);
    }
}
