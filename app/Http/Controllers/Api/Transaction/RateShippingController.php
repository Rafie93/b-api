<?php

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sistem\DistanceRates;

class RateShippingController extends Controller
{
    public function rate_check(Request $request)
    {
        $jarak = $request->jarak!=0 || $request->jarak != null ? $request->jarak : 1;
        $distance = DistanceRates::all()->first();

        if($jarak > $distance->max_km){
            return response()->json([
                'message' =>  'Tarif Pengiriman Tidak dijangkau'
              ], 400);
        }
        if($jarak < $distance->min_km){
            return response()->json([
                'message' =>  'Jarak Pengiriman Minimal '.$distance->min_km
              ], 400);
        }

        $tarif =  $jarak * $distance->prices;
        return response()->json([
          'tarif' =>  $tarif
        ], 200);
    }
}
