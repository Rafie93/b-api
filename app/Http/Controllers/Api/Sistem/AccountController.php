<?php

namespace App\Http\Controllers\Api\Sistem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Customer\Customer;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\Users\UserItem as UserResource;
use Carbon\Carbon;
use Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;

class AccountController extends Controller
{
    public $successStatus = 200;
    public function register(RegisterRequest $request)
    {
        $code =$this->generateCode();
        try
        {

            $usernameNull = explode('@',$request->email);
            DB::beginTransaction();
                $user = new \App\User;
                $user->role_id = 10;
                $user->is_active = '1';
                $user->name =  $request->name;
                $user->phone= $request->phone;
                $user->username= $request->username==null ? $usernameNull[0] : $request->username;
                $user->email= $request->email;
                $user->password = Hash::make($request->password);
                $user->fcm_token = $request->fcm_token;
                $user->role_id = 10;
                $user->group = 2;
                $user->remember_token = Str::random(60);
                $user->save();

                $member = Customer::create([
                    'user_id' => $user->id,
                    'code' => $code,
                    'name' => $request->name,
                    'type'=> 'general',
                ]);


            DB::commit();
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'success'=>false,
                'message'=> $e
            ], $this->successStatus);
        }

        return response()->json([
            'success'=>true,
            'message'=> "Registrasi berhasil"
        ], $this->successStatus);

    }

    public function login(Request $request)
    {
        $loginType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [
            $loginType => $request->username,
            'password' => $request->password,
            'group' => 2,
            'is_active'=>1
        ];

        $expirationInMinutes = Carbon::now()->addDays(10)->timestamp;
        JWTAuth::factory()->setTTL($expirationInMinutes);
        try {
            $isLogin = JWTAuth::attempt($credentials);
            if ($isLogin) {
                $customClaims = ['fullname' =>auth()->user()->name ,
                                 'user' => auth()->user()->id,
                                 'role'=>auth()->user()->role_id,
                                 'phone' => auth()->user()->phone,
                                 'email'=> auth()->user()->email,
                                 'fcm_token' => auth()->user()->fcm_token,
                                ];
                $token = JWTAuth::claims($customClaims)->attempt($credentials);
            }else{
                $us = User::where('email',$request->username)->orWhere('username',$request->username)->get()->count();
                if($us > 0){
                    return response()->json(['message' => 'Password Salah'], 400);
                }
                return response()->json(['message' => 'Email / Username Salah'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        return response()->json(compact('token'));
    }

    public function getAccount(Request $request)
    {
        $user = auth()->user();

        return response()->json([
            'status'=>"success",
            'account' =>new UserResource($user) ,
            'member' => Customer::where('user_id',$user->id)->first()
        ], 200);
    }

    public function changePassword(Request $request)
    {
        $user = auth()->user();
        $this->validate($request,[
            'password_old'=>'required',
            'password_new'=>'required|min:5',
            'password_confirmation' => 'required_with:password|same:password_new|min:5'
        ]);

        $hashedPassword = $user->password;
        if(Hash::check($request->password_old, $hashedPassword)){
            $user->update([
                'password'=>Hash::make($request->password_new)
            ]);
            return response()->json([
                'success'=>true,
                'message'=>"Password Anda Sudah Diperbaharui",
                'data'=>new UserResource($user)
            ], 200);
        }else{
            return response()->json([
                'success'=>false,
                'message'=> "Password Lama Salah"
            ], 200);
        }

    }


    public function generateCode()
    {
        $code = rand(10000000000,99999999999);
        if($this->codeNumberExists($code)){
            return generateCode();
        }
        return $code;
    }

    function codeNumberExists($number) {
        return Customer::where('code',$number)->exists();
    }

}
