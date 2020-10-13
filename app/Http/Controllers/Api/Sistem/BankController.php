<?php

namespace App\Http\Controllers\Api\Sistem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sistem\BankAccount;
use App\Http\Resources\Sistem\BankList as BankResource;
use App\Http\Resources\Sistem\BankItem as BankItemResource;

class BankController extends Controller
{
    public function index(Request $request)
    {
        $bank = BankAccount::orderBy('id','desc')->where('is_active',1)->get();
        return response()->json([
            'success'=>true,
            'banks'=> new BankResource($bank)
        ],200);
    }

    public function bank_account(Request $request)
    {
        $bank = BankAccount::orderBy('id','desc')->where('bank_name',$request->bank_name)->where('is_active',1)->first();
        return response()->json([
            'success'=>true,
            'bank_account'=> new BankItemResource($bank)
        ],200);
    }

    public function store(Request $request)
    {
        $bank = BankAccount::create($request->all());
        return response()->json([
            "success"=>true,
            "message" => "Data Bank Disimpan"
        ],200);
    }

    public function update(Request $request,$id)
    {
        $bank = BankAccount::where('id',$id)->update($request->all());
        return response()->json([
            "success"=>true,
            "message" => "Data Bank Disimpan"
        ],200);
    }
}
