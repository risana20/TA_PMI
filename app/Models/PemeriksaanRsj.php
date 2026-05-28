<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemeriksaanRsj extends Model
{
    use HasFactory;

    protected $fillable = [
        'warga_binaan_id', 'surat_rujukan_odgj_id', 'tgl_kontrol', 'kondisi', 'gejala', 'obat', 'catatan', 'kontrol_berikutnya',
    ];

    protected $casts = [
        'tgl_kontrol'        => 'date',
        'kontrol_berikutnya' => 'date',
    ];

    public function wargaBinaan()
    {
        return $this->belongsTo(WargaBinaan::class);
    }

    public function suratRujukanOdgj()
    {
        return $this->belongsTo(SuratRujukanOdgj::class);
    }
}
