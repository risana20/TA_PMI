<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Artikel extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'judul', 'slug', 'kategori', 'konten', 'gambar', 'status', 'tgl_terbit',
    ];

    protected $casts = [
        'tgl_terbit' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($artikel) {
            $artikel->slug = Str::slug($artikel->judul) . '-' . time();
        });
    }

    public function penulis()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'PUBLISHED');
    }
}
