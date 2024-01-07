<?php

namespace App\Http\Controllers;

use App\Aspects\Addmembers;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\RefreshToken;
use Laravel\Passport\Token;


class UserController extends Controller
{
    use ResponseTrait;
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => "V01",
                'msg' => $validator->errors()
            ]);
        }
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('MyApp')->accessToken;
            return response()->json([
                'success' => true,
                'user_token' => $token,
                'msg' => 'login successfully'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => "A01",
                'msg' => 'Email or Password not correct'
            ]);
        }
    }

    public function logout():JsonResponse{
        $user = Auth::user()->token();
        $user->revoke();
        return $this->returnSuccess("","logout successfully");


    }
}
