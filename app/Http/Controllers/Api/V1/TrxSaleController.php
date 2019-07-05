<?php

namespace App\Http\Controllers\Api\V1;

use App\TrxSale;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TrxSaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TrxSale::orderBy('id', 'desc')->paginate(10);
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
            'pay' => 'required|integer',
            'details' => 'required|array',
            'details.*.product_id' => 'required|integer',
            'details.*.qty' => 'required|integer'
        ]);

        return TrxSale::saveTransaction($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return TrxSale::with([
            'details.product.category' => function ($q) {
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
