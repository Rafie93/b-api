<?php

namespace App\Http\Controllers\Api\Sistem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Models\Orders\Order;
use App\Models\Purchases\Purchase;
use JWTAuth;
use App\Http\Resources\Users\UserList as UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\Users\UserItem as ItemResource;

class UserController extends Controller
{
    public $user;
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index(Request $request)
    {
        if($this->user->role_id==1 || $this->user->role_id==2){
            $users = User::where('group',1)->get();
            return response()->json([
                'success' => true,
                'users' =>  new UserResource($users)
               ],200);
        }else{
            return response()->json([
                'success' => false,
                'message' =>  "Not Access"
               ],400);
        }
    }
    public function edit($id)
    {
        $users = User::where('id',$id)->first();
        return response()->json([
            'users'=>new ItemResource($users)
           ],200);
    }

    public function update(Request $request, $id)
    {
        if($this->user->role_id==1){
            $user = User::where('id',$id)
                ->update([
                'username' =>  $request->get('username'),
                'phone' => $request->get('phone'),
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'gender' => $request->get('gender'),
                'penanggung_id' => $request->get('penanggung_id'),
                'role_id' =>  $request->get('role_id') ? $request->get('role_id') : 0,
            ]);

            $password = $request->get('user_password');
            if($password!=""){
                $user = User::where('id',$id)
                        ->update([
                            'password' =>Hash::make($password)
                        ]);
            }
             return response()->json([
                 'success'=>true,
                 'users' => $user,
                 'message' => 'Data Berhasil Di Perbaharui'
             ]);
         }else{
            return response()->json([
                'success' => false,
                'message' =>  "Not Access"
               ],400);
         }

    }

    public function store(Request $request)
    {
        if($this->user->role_id==1){
            $username = $request->get('username');
            $email =  $request->get('email');
            $cekUserUsername = $this->isValid('username',$username);
            $cekEmail = $this->isValid('email',$email);

            if($cekUserUsername>0){
                return response()->json([
                    'success' => false,
                    'message' =>  "Username tidak valid"
                   ],400);
            }
            if($cekEmail>0){
                return response()->json([
                    'success' => false,
                    'message' =>  "Email sudah terdaftar"
                   ],400);
            }
            $user = User::create([
                'username' => $username,
                'phone' => $request->get('phone'),
                'name' => $request->get('name'),
                'email' =>$email,
                'gender' => $request->get('gender'),
                'penanggung_id' => $request->get('penanggung_id'),
                'role_id' =>  $request->get('role_id') ? $request->get('role_id') : 0,
                'password' => Hash::make($request->get('user_password')),
                'is_active'=> $request->get('is_active') ? $request->get('is_active') : 1
            ]);
             return response()->json([
                 'success'=>true,
                 'users' => $user,
                 'message' => 'Data Berhasil Disimpan'
             ]);
         }else{
            return response()->json([
                'success' => false,
                'message' =>  "Not Access"
               ],400);
         }
    }
    public function isValid($column,$data)
    {
        return User::where($column,$data)->get()->count();
    }

    public function delete($id)
    {
        $sp = Order::where('creator_id',$id)->get();
        $pc = Purchase::where('creator_id',$id)->get();
        if(empty($sp) && empty($sp)){
            $user = User::find($id);
            $user->delete();
            return response()->json([
                'success' => true,
                'message' =>  "User Berhasil dihapus"
               ],200);
        }else{
            return response()->json([
                'success' => false,
                'message' =>  "User Tidak dapat dihapus"
               ],400);
        }
    }
}
