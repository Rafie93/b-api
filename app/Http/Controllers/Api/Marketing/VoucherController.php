<?php

namespace App\Http\Controllers\Api\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promotion\Voucher;
use App\Http\Resources\Promotion\VoucherList as VoucherResource;
use App\Http\Resources\Promotion\Vouchertem as VoucherItemResource;

class VoucherController extends Controller
{
    public function list(Request $request)
    {
        $voucher = Voucher::orderBy('id','desc')
                            ->when($request->jenis, function ($query) use ($request) {
                                $query->where('jenis_voucher', '=',$request->jenis);
                            })
                            ->get();

        return response()->json([
            'success'=>true,
            'vouchers'=> new VoucherResource($voucher)
        ],200);
    }

    public function store(Request $request)
    {
        $berlaku_start =replaceDate($request->berlaku_start);
        $berlaku_end =replaceDate($request->berlaku_end);
        $request->merge([
            'berlaku_start' => $berlaku_start,
            'berlaku_end'   => $berlaku_end
        ]);

        $unit = Voucher::create($request->all());
        return response()->json([
            'success' => true,
            'message' =>  "Voucher Berhasil ditambahkan"
           ],200);
    }

    public function update(Request $request,$id)
    {
        $berlaku_start =replaceDate($request->berlaku_start);
        $berlaku_end =replaceDate($request->berlaku_end);
        $request->merge([
            'berlaku_start' => $berlaku_start,
            'berlaku_end'   => $berlaku_end
        ]);

        $voucher = Voucher::find($id);
        $voucher->update($request->all());
        return response()->json([
            'success' => true,
            'message' =>  "voucher Berhasil diubah"
           ],200);
    }

    public function delete(Request $request,$id)
    {
        $voucher = Voucher::find($id);
        $voucher->delete();
        return response()->json([
            'success' => true,
            'message' =>  "Voucher Berhasil dihapus"
           ],200);
    }
}
