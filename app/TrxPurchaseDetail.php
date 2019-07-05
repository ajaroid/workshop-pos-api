<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrxPurchaseDetail extends Model
{
    protected $fillable = [
        'trx_purchase_id',
        'product_id',
        'qty',
        'subtotal'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
