<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sales\Promotion;
use App\Models\Sales\PromotionDetail;
use JWTAuth;
use Illuminate\Support\Facades\DB;
use App\User;

class PromoBelanjaController extends Controller
{
    public $user;
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }
    public function index(Request $request)
    {
        $data = Promotion::orderBy('id','desc')->get();
        return response()->json([
            'success' => true,
            'promotions' => $data
           ],200);
    }

    public function detail(Request $request,$id)
    {
        $promos =PromotionDetail::where('promotion_id',$id)->get();
        $out=[];
        foreach ($promos as $item) {
            $out[] = array(
                'id'=>$item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'price' =>$item->price_promo
            );
        }
        return response()->json([
            'success' => true,
            'promotions' => Promotion::where('id',$id)->first(),
            'promotion_id' => $id,
            'detail' => $out
           ],200);
    }

    public function store(Request $request)
    {
        try{
            DB::beginTransaction();
                $promos = Promotion::create([
                    'date_start' => replaceDate($request->date_start),
                    'date_end' => replaceDate($request->date_end),
                    'min_shopping' => $request->min_shopping,
                    'description' => $request->description,
                    'type_promo' => $request->type_promo,
                    'option_promo' =>  $request->type_promo=='Tebus Murah' ? $request->option_promo : 0,
                    'total'=> $request->type_promo=='Diskon Belanja' ? $request->total : 0,
                    'is_active'=>1
                ]);

                $pakets = $request->paket;
                foreach($pakets as $s){
                    $product_id = $s["product_id"];
                    $price = $s["price"];
                    PromotionDetail::create([
                        'product_id'=>$product_id,
                        'promotion_id' => $promos->id,
                        'price_promo' =>  $price
                    ]);

                }
            DB::commit();
            return response()->json([
                'success' => true,
                'product' =>  $promos
            ],200);
        }catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' =>  "Gagal membuat promo belanja"
            ],400);
        }
    }

    public function update(Request $request,$id)
    {
        try{
            DB::beginTransaction();
                $promos = Promotion::where('id',$id)->update([
                    'date_start' => replaceDate($request->date_start),
                    'date_end' => replaceDate($request->date_end),
                    'min_shopping' => $request->min_shopping,
                    'type_promo' => $request->type_promo,
                    'description' => $request->description,
                    'option_promo' =>  $request->type_promo=='Tebus Murah' ? $request->option_promo : 0,
                    'total'=> $request->type_promo=='Diskon Belanja' ? $request->total : 0,
                    'is_active'=>$request->is_active
                ]);

                $pakets = $request->paket;
                foreach($pakets as $s){
                    $product_id = $s["product_id"];
                    $price = $s["price"];
                    $promoDataStore = [
                        'product_id'      => $product_id,
                        'promotion_id'       => $id,
                        'price_promo'           => $price,
                    ];
                    DB::table('promotion_detail')->updateOrInsert([
                        'product_id' => $product_id,
                        'promotion_id' => $id
                    ],$promoDataStore);
                }
            DB::commit();
            return response()->json([
                'success' => true,
                'product' =>  $promos
            ],200);
        }catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' =>  "Gagal membuat promo belanja"
            ],400);
        }
    }

    public function delete(Request $request,$id)
    {
        $promo = Promotion::find($id);
        $promo->delete($promo);

        $promoDetail = PromotionDetail::where('promotion_id',$id)->delete();
        return response()->json([
            'success' => true,
            'message' =>  'Promo Berhasil Dihapus'
        ],200);
    }

    //CHECK BELANJA SAAT DIKASIR
    public function checkPromo(Request $request)
    {
        $now = date('Y-m-d');
        $belanjaTotal = $request->total_price;
        $promosi = Promotion::orderBy('min_shopping','desc')
                    ->where('date_start','<=',$now)
                    ->where('date_end','>=',$now)
                    ->where('min_shopping','>=',$belanjaTotal)
                    ->get();
        if($promosi->count() > 0){
            $promosi = $promosi->first();
            return response()->json([
                'success' => true,
                'message' =>  $promosi->description,
                'data' => $promosi
            ],200);
        }else{
            return response()->json([
                'success' => false,
                'message' =>  'Tidak Ada Promo Belanja Hari ini'
            ],400);
        }
    }
}
