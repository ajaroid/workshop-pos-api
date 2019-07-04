<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrxPurchase extends Model
{
    protected $fillable = [
        'supplier_id',
        'user_id',
        'total'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function details()
    {
        return $this->hasMany(TrxPurchaseDetail::class);
    }

}
