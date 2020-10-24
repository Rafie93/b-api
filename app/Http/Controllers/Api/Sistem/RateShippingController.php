<?php

namespace App\Http\Controllers\Api\Sistem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sistem\DistanceRates;

class RateShippingController extends Controller
{
   public function list(Request $request)
   {
      $tarif = DistanceRates::all();
      return response()->json([
        'success'=>true,
        'tarif' =>  $tarif
      ], 200);
   }

   public function update(Request $request,$id)
   {
       $tarif = DistanceRates::where('id',$id)->first();
       $tarif->update($request->all());
       return response()->json([
        'success'=>true,
        'message' =>  "Tarif Berhasil di Perbaharui"
      ], 200);
   }
}
