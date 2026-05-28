<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatPenyakit extends Model
{
    use HasFactory;

    protected $fillable = ['warga_binaan_id', 'monitoring_kesehatan_id', 'nama_penyakit', 'status', 'tanggal'];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function wargaBinaan()
    {
        return $this->belongsTo(WargaBinaan::class);
    }

    public function monitoringKesehatan()
    {
        return $this->belongsTo(MonitoringKesehatan::class);
    }
}
