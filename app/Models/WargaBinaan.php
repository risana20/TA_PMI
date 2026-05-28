<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SuratRujukanOdgj;

class WargaBinaan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nik',
        'nama',
        'tempat_lahir',
        'tgl_lahir',
        'alamat',
        'jenis_kelamin',
        'kategori',
        'status',
        'tgl_masuk',
        'no_bpjs',
        'catatan',
        'penanggung_jawab',
        'kontak_pj',
        'foto',
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
        'tgl_masuk' => 'date',
    ];

    public function getUmurAttribute(): int
    {
        return Carbon::parse($this->tgl_lahir)->age;
    }

    public function getJenisKelaminTextAttribute()
    {
        if ($this->attributes['jenis_kelamin'] === 'L') {
            return 'Laki-laki';
        }
        if ($this->attributes['jenis_kelamin'] === 'P') {
            return 'Perempuan';
        }
        return 'Tidak Diketahui';
    }

    public function monitoringKesehatans()
    {
        return $this->hasMany(MonitoringKesehatan::class);
    }

    public function pemeriksaanRsjs()
    {
        return $this->hasMany(PemeriksaanRsj::class);
    }

    public function riwayatPenyakits()
    {
        return $this->hasMany(RiwayatPenyakit::class);
    }

    public function daftarSemuaHistoriPenyakit()
    {
        return $this->hasMany(RiwayatPenyakit::class)->orderBy('created_at', 'desc');
    }

    public function penyakitPernahAktifTerkini()
    {
        return $this->hasMany(RiwayatPenyakit::class)
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                      ->from('riwayat_penyakits as rp')
                      ->whereColumn('rp.warga_binaan_id', 'riwayat_penyakits.warga_binaan_id')
                      ->groupBy('rp.nama_penyakit');
            })
            ->where('status', 'Aktif');
    }

    /**
     * Relation for surat rujukan ODGJ.
     */
    public function suratRujukanOdgjs()
    {
        return $this->hasMany(SuratRujukanOdgj::class);
    }

    /**
     * Relation for stok obat (medicine tracking).
     */
    public function stokObats()
    {
        return $this->hasMany(StokObat::class);
    }

    public function scopeOdgj($query)
    {
        return $query->where('kategori', 'ODGJ');
    }

    public function scopeLansia($query)
    {
        return $query->where('kategori', 'Lansia');
    }

    public function pengeluaranLogistiks()
    {
        return $this->hasMany(PengeluaranLogistik::class);
    }
}
