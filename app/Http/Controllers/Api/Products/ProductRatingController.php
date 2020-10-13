<?php

namespace App\Http\Controllers\Api\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;
use App\Models\Products\ProductRating;

class ProductRatingController extends Controller
{
    public $user;
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function getRating(Request $request)
    {
        $productRating = ProductRating::where('sales_id',$request->sales_id)->where('product_id',$request->product_id);
        return response()->json([
            'success' => true,
            'message' =>  "Produk sudah dinilai"
           ],200);
    }

    public function rating_create(Request $request)
    {
        $request->merge([
            'sales_id' => $request->sales_id,
            'creator_id' => $this->user->id,
            'product_id' => $request->product_id
        ]);

        $productRating = ProductRating::create($request->all());
        return response()->json([
            'success' => true,
            'message' =>  "Produk sudah dinilai"
           ],200);
    }

}
