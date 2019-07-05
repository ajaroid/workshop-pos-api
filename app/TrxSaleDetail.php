<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrxSaleDetail extends Model
{
    protected $fillable = [
        'trx_sale_id',
        'product_id',
        'qty',
        'subtotal'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
