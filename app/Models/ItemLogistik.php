<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemLogistik extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'item_logistiks';

    protected $fillable = [
        'jenis_logistik_id',
        'nama_item',
        'satuan',
    ];

    public function jenisLogistik()
    {
        return $this->belongsTo(JenisLogistik::class);
    }

    public function stokLogistiks()
    {
        return $this->hasMany(StokLogistik::class);
    }
    
    public function detailReimbursements()
    {
        return $this->hasMany(DetailReimbursement::class);
    }
}
