<?php

namespace App\Http\Controllers\Api\Sistem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promotion\Voucher;
use App\Http\Resources\Promotion\VoucherList as VoucherResource;
use App\Http\Resources\Promotion\Vouchertem as VoucherItemResource;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $now = date('Y-m-d');
        $voucher = Voucher::orderBy('id','desc')
                            ->where('is_active',1)
                            ->where('berlaku_start','<=',$now)
                            ->where('berlaku_end','>=',$now)
                            ->when($request->jenis, function ($query) use ($request) {
                                $query->where('jenis_voucher', '=',$request->jenis);
                            })
                            ->get();
        return response()->json([
            'success'=>true,
            'vouchers'=> new VoucherResource($voucher)
        ],200);
    }

    public function checkVoucher(Request $request)
    {
        $now = date('Y-m-d');
        $voucher = Voucher::orderBy('id','desc')
                            ->where('code_voucher',$request->code_voucher)
                            ->where('is_active',1)
                            ->where('berlaku_start','<=',$now)
                            ->where('berlaku_end','>=',$now)
                            ->get();

        if($voucher->count()==1){
            return response()->json([
                'valid'=>true,
                'code' => $request->code_voucher,
                'nilai'=>$voucher->first()->nilai,
                'jenis_voucher' => $voucher->first()->jenis_voucher,
                'jenis_nilai' =>$voucher->first()->jenis_nilai,
                'maksimal'=>$voucher->first()->maksimal,
            ],200);
        }
        return response()->json([
            'valid'=>false,
        ],400);

    }
}
