<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer\Customer;
use App\Models\Sales\Sale;
use App\Models\Sistem\NumberSequence;
use App\User;
use App\Http\Resources\Customer\CustomerList as CustomerResoure;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
          $list = Customer::orderBy('name','asc')
                    ->get();

        return response()->json([
            'success' => true,
            'customers' =>  new CustomerResoure($list)
           ],200);
    }
    public function store(Request $request)
    {
        $this->validate($request,[
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
        ]);

        $no_hp = $request->phone;
        $email = $request->email;

        $cekHp = User::where('phone',$no_hp)->where('is_active',1)->get()->count();
        $cekEmail = User::where('email',$no_hp)->where('is_active',1)->get()->count();

        if($cekHp > 0){
            return response()->json([
                'success'=>false,
                'message'=>'No HP Sudah Terdaftar'
            ], 400);
        }
        if($cekEmail > 0){
            return response()->json([
                'success'=>false,
                'message'=>'Email Sudah Terdaftar'
            ], 400);
        }

        try
        {
             DB::beginTransaction();
                $user = new \App\User;
                $user->role_id = 10; // Role for kasir
                $user->name =  $request->name;
                $user->email = $email;
                $user->username = $email;
                $user->phone = $no_hp;
                $user->birthday = $this->replaceDate($request->birthday);
                $user->gender = $request->gender;
                $user->group = 2;
                $user->password = Hash::make($no_hp);
                $user->save();

                $request->merge([
                    'user_id' => $user->id,
                    'code'=> $this->generateCode() ,
                    'point' => 0
                ]);
                $customer = Customer::create($request->all());
             DB::commit();

             return response()->json([
                'success' => true,
                'message' =>  "Customer Berhasil ditambahkan"
               ],200);
        }catch(\PDOException $e){
            DB::rollBack();
            return response()->json([
                'success'=>false,
                'message'=>$e
            ], 400);
        }


    }

    public function update(Request $request,$id)
    {
        $customer = Customer::find($id);
        $customer->update($request->all());
        return response()->json([
            'success' => true,
            'message' =>  "Customer Berhasil diubah"
           ],200);
    }

    public function delete(Request $request,$id)
    {
        $sp = Sale::where('customer_id',$id)->get()->count();
        if ($sp<=0){
            $customer = Customer::find($id);
            $userId = $customer->user_id;
            $customerDel = $customer->delete();
            if($customerDel){
                User::where('id',$userId)->delete();
            }
            return response()->json([
                'success' => true,
                'message' =>  "Customer Berhasil dihapus"
               ],200);
        }else{
            return response()->json([
                'success' => false,
                'message' =>  "Customer Tidak dapat dihapus"
               ],400);
        }

    }

    public function generateCode()
    {
        $tahun = date('Y');
        $numberData = NumberSequence::where('seq_name','MB')
                                    ->get();

        $number=1;
        if(count($numberData)>0){
            $number = $numberData->first()->seq_value+1;
            NumberSequence::where('seq_name','MB')
                            ->update([
                                'seq_value'=>$number
                            ]);
        }else{
            NumberSequence::insert([
                'seq_value'=>$number,
                'seq_year'=>$tahun,
                'seq_name'=>'MB'
            ]);
        }

        if($number<10){
            $number = "000".$number;
        }elseif($number<100){
            $number = "00".$number;
        }else if($number<1000){
            $number = "0".$number;
        }
        return 'MB'.$number;
    }

    public function replaceDate($date)
    {
        return date("Y-m-d", strtotime($date));
    }
}
