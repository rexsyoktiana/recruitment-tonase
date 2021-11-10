<?php

namespace App\Http\Controllers\Api;

use App\Helpers\LogActivity;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials    = $request->only('email', 'password');
        $token          = null;
        try {
            $token = JWTAuth::attempt($credentials);
            if (!$token = JWTAuth::attempt($credentials)) {
                $response = [
                    'status'    =>  'error',
                    'data'      =>  [],
                    'message'   =>  'invalid credentials'
                ];
                return response()->json($response, 400);
            }
        } catch (JWTException $e) {
            $response = [
                'status'    =>  'error',
                'data'      =>  [],
                'message'   =>  'could not create token'
            ];
            return response()->json($response, 500);
        }

        LogActivity::addToLog($request, "Login");
        $response = [
            'status'    =>  'success',
            'data'      =>  $token,
            'message'   =>  'berhasil login'
        ];
        return response()->json($response, 200);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required', 'string', 'confirmed', Password::min(7)
                    ->letters()
                    ->numbers()
                    ->symbols()
            ],
        ]);

        if ($validator->fails()) {
            $response = [
                'status'    =>  'error',
                'data'      =>  [],
                'message'   =>  json_decode($validator->errors(), true, JSON_UNESCAPED_SLASHES),
            ];
            return response()->json($response, 400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        $token = JWTAuth::fromUser($user);

        $user['token']  = $token;

        $response = [
            'status'    =>  'success',
            'data'      =>  $user,
            'message'   =>  'berhasil register akun'
        ];
        return response()->json($response, 201);
    }
}
