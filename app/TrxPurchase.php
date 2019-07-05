<?php

namespace App;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public static function saveTransaction(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $details = collect($request->details);

            $productIds = $details->pluck('product_id')->all();

            $products = Product::findOrFail($productIds);

            $products = $products->map(function ($item, $key) use ($details) {

                $item['qty'] = $details->filter(function ($d) use ($item) {
                    return $d['product_id'] == $item['id'];
                })->values()->get(0)['qty'];

                $stock = $item['stock'] + $item['qty'];
                $subtotal = $item['price_purchase'] * $item['qty'];

                Product::where('id', $item['id'])->update(['stock' => $stock]);

                return [
                    'product_id' => $item['id'],
                    'qty' => $item['qty'],
                    'subtotal' => $subtotal
                ];
            })->all();

            $total = collect($products)->sum('subtotal');

            $trx = TrxPurchase::create([
                'supplier_id' => $request->supplier_id,
                'user_id' => Auth::id(),
                'total' => $total,
            ]);

            $data = collect($products)->map(function ($item, $key) use ($trx) {
                $item['trx_purchase_id'] = $trx->id;
                return $item;
            })->all();

            TrxPurchaseDetail::insert($data);

            return $trx;
        });
    }
}
