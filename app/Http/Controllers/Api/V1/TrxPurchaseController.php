<?php

namespace App\Http\Controllers\Api\V1;

use Auth;
use App\Product;
use App\TrxPurchase;
use App\TrxPurchaseDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TrxPurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TrxPurchase::with([
            'supplier' => function ($q) {
                $q->select('id', 'name', 'email', 'phone', 'address');
            }
        ])
        ->orderBy('id', 'desc')
        ->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|integer',
            'details.*.product_id' => 'required|integer',
            'details.*.qty' => 'required|integer'
        ]);

        $details = collect($request->details);

        $productIds = $details->pluck('product_id')->all();

        $products = Product::findOrFail($productIds);
        $products = $products->map(function ($item, $key) use ($details) {

            $item['qty'] = $details->filter(function ($d) use ($item) {
                return $d['product_id'] == $item['id'];
            })->values()->get(0)['qty'];

            $item['subtotal'] = $item['price_purchase'] * $item['qty'];

            return [
                'product_id' => $item['id'],
                'qty' => $item['qty'],
                'subtotal' => $item['subtotal']
            ];
        })->all();

        $total = collect($products)->sum('subtotal');

        return DB::transaction(function () use ($request, $products, $total) {

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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return TrxPurchase::with([
            'supplier' => function ($q) {
                $q->select('id', 'name', 'email', 'phone', 'address');
            },
            'details' => function ($q) {
                $q->orderBy('id', 'desc');
            }
        ])->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
