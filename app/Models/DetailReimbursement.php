<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use  Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailReimbursement extends Model
{
    use HasFactory;

    protected $fillable = [
        'reimbursement_id', 'nama_kebutuhan', 'nominal', 'jenis_logistik_id'
    ];
    public function reimbursement()
    {
        return $this->belongsTo(Reimbursement::class);
    }

    public function jenisLogistik()
    {
        return $this->belongsTo(JenisLogistik::class);
    }
}
