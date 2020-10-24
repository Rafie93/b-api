<?php

namespace App\Http\Controllers\Api\Sistem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sistem\BankAccount;
use App\Http\Resources\Sistem\BankList as BankResource;
use App\Http\Resources\Sistem\BankItem as BankItemResource;
use File;
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

    public function list(Request $request)
    {
        $bank = BankAccount::orderBy('id','desc')->get();
        return response()->json([
            'success'=>true,
            'banks'=> new BankResource($bank)
        ],200);
    }

    public function edit(Request $request,$id)
    {
        $bank = BankAccount::orderBy('id','desc')->where('id',$id)->first();
        return response()->json([
            'success'=>true,
            'bank_account'=> new BankItemResource($bank)
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
        if ($request->hasFile('file')) {
            $requestData = $request->data;
            $someRequest = json_decode($requestData, true);

            $bank = BankAccount::create([
                'bank_name'=>$someRequest['bank_name'],
                'description'=>$someRequest['description'],
                'bank_account_no' => $someRequest['bank_account_no'],
                'bank_account_name' => $someRequest['bank_account_name']
            ]);
            $foto = $request->file('file');
            $fileName = $foto->getClientOriginalName();
            $request->file('file')->move('images/bank',$fileName);
            $fotoUpdate = BankAccount::where('id',$bank->id)
                                  ->update(['bank_logo' => $fileName]);
            return response()->json([
                'success'=>true,
                'message'=>'Data Bank Berhasil ditambahkan'
             ], 200);

        }else{
            $bank = BankAccount::create($request->all());
            return response()->json([
                "success"=>true,
                "message" => "Data Bank Disimpan"
            ],200);
        }


    }

    public function update(Request $request,$id)
    {
        $bank = BankAccount::where('id',$id)->first();
        if ($request->hasFile('file')) {
            $requestData = $request->data;
            $someRequest = json_decode($requestData, true);
            $bank->update([
                'bank_name'=>$someRequest['bank_name'],
                'description'=>$someRequest['description'],
                'bank_account_no' => $someRequest['bank_account_no'],
                'bank_account_name' => $someRequest['bank_account_name']
            ]);
            $foto = $request->file('file');
            $fileName = $foto->getClientOriginalName();
            $request->file('file')->move('images/bank',$fileName);
            $fotoUpdate = BankAccount::where('id',$id)
                                  ->update(['bank_logo' => $fileName]);

            return response()->json([
                'success'=>true,
                'message'=>'Data Bank Berhasil di Perbaharui'
            ], 200);

        }else{
            $bank = BankAccount::where('id',$id)->update($request->all());
            return response()->json([
                "success"=>true,
                "message" => "Data Bank Di Perbaharui"
            ],200);
        }
    }

    public function delete(Request $request,$id)
    {
        $bankImage = BankAccount::find($id);
        $path = 'images/bank/';
        File::delete($path.$bankImage->bank_logo);
        $bankImage->delete();
        return response()->json([
            'success' => true,
            'message' =>  "Data Bank Berhasil dihapus"
           ],200);
    }
}
