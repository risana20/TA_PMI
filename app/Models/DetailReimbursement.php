<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use  Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailReimbursement extends Model
{
    use HasFactory;

    protected $fillable = [
        'reimbursement_id', 'item_logistik_id', 'jumlah', 'nominal'
    ];
    public function reimbursement()
    {
        return $this->belongsTo(Reimbursement::class);
    }

    public function itemLogistik()
    {
        return $this->belongsTo(ItemLogistik::class);
    }
}
