<?php

namespace App\Http\Controllers\Api;

use App\Helpers\LogActivity;
use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;

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
            'data'      =>  [
                'id'    =>  Auth()->user()->id,
                'email' =>  Auth()->user()->email,
                'name'  =>  Auth()->user()->name,
                'token' =>  $token,
            ],
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
            'rekening'  =>  'required|integer|unique:users,rekening|min:8'
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
            'rekening'  =>  $request->get('rekening'),
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

    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            $response = [
                'status'    =>  'error',
                'data'      =>  [],
                'message'   =>  json_decode($validator->errors(), true, JSON_UNESCAPED_SLASHES),
            ];
            return response()->json($response, 400);
        }

        $token = Str::random(64);


        PasswordReset::create([
            'email' => $request->email,
            'token' => $token,
        ]);

        $details = [
            'title' => 'Mail from TONASE',
            'body'  => 'Silahkan gunakan token dibawah ini untuk ganti password kamu',
            'token' =>  $token,
        ];

        Mail::to($request->email)->send(new PasswordResetMail($details));

        $response = [
            'status'    =>  'success',
            'data'      =>  [],
            'message'   =>  'Token berhasil dikirim ke email'
        ];

        return response()->json($response, 200);
    }

    public function changePassword(Request $request, $token)
    {
        $validator = Validator::make($request->all(), [
            'email'     => 'required|email|exists:users,email',
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

        $email = $request->email;
        $password = $request->password;
        $check = PasswordReset::where('email', '=', $email)->where('token', '=', $token)->first();

        if (empty($check)) {
            $response = [
                'status'    =>  'error',
                'data'      =>  [],
                'message'   =>  'email dan token tidak cocok',
            ];
            return response()->json($response, 400);
        }

        $checkPassword = User::where('email', '=', $email)->first();

        if (password_verify($password, $checkPassword->password)) {
            $response = [
                'status'    =>  'error',
                'data'      =>  [],
                'message'   =>  'Password harus berbeda dengan sebelumnya',
            ];
            return response()->json($response, 400);
        }

        User::where('email', '=', $email)->update(['password' => Hash::make($password)]);
        $response = [
            'status'    =>  'success',
            'data'      =>  [],
            'message'   =>  'anda berhasil mengubah password',
        ];
        return response()->json($response, 400);
    }
}
