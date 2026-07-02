<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kunjungan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'nama_pengunjung', 'no_hp',
         'tujuan','instansi', 'tgl_kunjungan', 'jam',
         'surat_pengajuan', 'status', 'alasan_tolak', 'warga_binaan_id',
    ];

    protected $casts = [
        'tgl_kunjungan' =>  'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wargaBinaan()
    {
        return $this->belongsTo(WargaBinaan::class);
    }

    public function getFormattedJamAttribute()
    {
        $jamStr = $this->jam;
        if (!$jamStr) return '-';

        $jamStr = trim($jamStr);
        if (stripos($jamStr, 'Sesi') !== false) {
            return $jamStr;
        }

        $normalized = str_replace('.', ':', $jamStr);
        if (preg_match('/(\d{2}):(\d{2})/', $normalized, $matches)) {
            $hour = (int)$matches[1];
            $min = (int)$matches[2];
            $totalMinutes = $hour * 60 + $min;

            if ($totalMinutes >= 480 && $totalMinutes < 570) return 'Sesi 1: 08.00-09.30';
            if ($totalMinutes >= 570 && $totalMinutes < 660) return 'Sesi 2: 09.30-11.00';
            if ($totalMinutes >= 660 && $totalMinutes < 780) return 'Sesi 3: 11.00-12.30';
            if ($totalMinutes >= 780 && $totalMinutes < 870) return 'Sesi 4: 13.00-14.30';
            if ($totalMinutes >= 870 && $totalMinutes <= 990) return 'Sesi 5: 14.30-16.00';
        }

        return $this->jam;
    }
}
