<?php

namespace App\Http\Controllers\Api\V1;

use Auth;
use App\Product;
use App\TrxPurchase;
use Illuminate\Http\Request;
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
            'details' => 'required|array',
            'details.*.product_id' => 'required|integer',
            'details.*.qty' => 'required|integer'
        ]);

        return TrxPurchase::saveTransaction($request);
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
            'details.product' => function ($q) {
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
        //nothing
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //nothing
    }
}
