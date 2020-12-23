<?php

namespace App\Http\Controllers\Api\Sistem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sales\Promotion;
use App\Models\Sales\PromotionDetail;

class CheckDiskonController extends Controller
{
    public function check(Request $request,$total)
    {
        $dateNow = date('Y-m-d');
        // $total  = $request->total;
        $promotion = Promotion::orderBy('id','asc')
                    ->where('date_start','<=',$dateNow)
                    ->where('date_end','>=',$dateNow)
                    ->where('min_shopping','<=',$total)
                    ->where('is_active',1)
                    ->get();

        $data = null;
        $detail = array();
        $promo_type = null;
        if($promotion->count()>0){
            $data = $promotion->first();
            $promo_type = $data->type_promo;
            if($data->type_promo=='Tebus Murah'){
                $detail = PromotionDetail::where('promotion_id',$data->id)->get();
            }
        }
        return response()->json([
                        'success'=>true,
                        'promo' => $promotion->count() > 0 ? true : false,
                        'promo_type' => $promo_type,
                        'data'=> $data,
                        'detail' => $detail
                    ],200);

    }
}
