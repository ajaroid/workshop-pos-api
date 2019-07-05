<?php

namespace App;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class TrxSale extends Model
{
    protected $fillable = [
        'total',
        'pay',
        'user_id',
        'change'
    ];

    public function details()
    {
        return $this->hasMany(TrxSaleDetail::class);
    }

    public static function saveTransaction(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $pay = $request->pay;

            $details = collect($request->details);

            $productIds = $details->pluck('product_id')->all();

            $products = Product::findOrFail($productIds);

            $products = $products->map(function ($item, $key) use ($details) {

                $item['qty'] = $details->filter(function ($d) use ($item) {
                    return $d['product_id'] == $item['id'];
                })->values()->get(0)['qty'];

                if ($item['qty'] > $item['stock']) {
                    throw ValidationException::withMessages([
                        'details.*.qty' => ['Not enough stock']
                    ]);
                }

                $stock = $item['stock'] - $item['qty'];
                $subtotal = $item['price_sale'] * $item['qty'];

                Product::where('id', $item['id'])->update(['stock' => $stock]);

                return [
                    'product_id' => $item['id'],
                    'qty' => $item['qty'],
                    'subtotal' => $subtotal
                ];
            })->all();

            $total = collect($products)->sum('subtotal');

            if ($pay < $total) {
                throw ValidationException::withMessages([
                    'pay' => ['Not enough money']
                ]);
            }

            $trx = TrxSale::create([
                'user_id' => Auth::id(),
                'total' => $total,
                'pay' => $pay,
                'change' => $pay > $total ? ($pay - $total) : 0
            ]);

            $data = collect($products)->map(function ($item, $key) use ($trx) {
                $item['trx_sale_id'] = $trx->id;
                return $item;
            })->all();

            TrxSaleDetail::insert($data);

            return $trx;
        });
    }
}
