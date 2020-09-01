<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;
use App\Http\Requests\RegisterRequest;
use Carbon\Carbon;
use Auth;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $loginType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [
            $loginType => $request->username,
            'password' => $request->password,
            'is_active'=>1
        ];

        $expirationInMinutes = Carbon::now()->addDays(1)->timestamp;
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
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        return response()->json(compact('token'));
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'username' => $request->get('username'),
            'phone' => $request->get('phone'),
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'role_id' =>  $request->get('role_id') ? $request->get('role_id') : 0,
            'password' => Hash::make($request->get('password')),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user','token'),201);
    }

    public function getAuthenticatedUser()
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        return response()->json(compact('user'));
    }
}
