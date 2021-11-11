<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Topup;
use App\Models\Transfer;
use App\Models\User;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class PaymentController extends Controller
{
    public function topUp(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'jumlah'     => 'required|integer',
        ]);

        if ($validator->fails()) {
            $response = [
                'status'    =>  'error',
                'data'      =>  [],
                'message'   =>  json_decode($validator->errors(), true, JSON_UNESCAPED_SLASHES),
            ];
            return response()->json($response, 400);
        }
        $jumlah = $request->jumlah;
        User::where('id', '=', $user->id)->update(['saldo' => $user->saldo + $jumlah]);

        Topup::create([
            'user'      =>  $user->id,
            'jumlah'    =>  $jumlah
        ]);

        $response = [
            'status'    =>  'success',
            'data'      =>  User::where('id', '=', $user->id)->first(),
            'message'   =>  'anda berhasil menambahkan saldo'
        ];
        return response()->json($response, 200);
    }

    public function withDraw(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'jumlah'     => 'required|integer',
        ]);

        if ($validator->fails()) {
            $response = [
                'status'    =>  'error',
                'data'      =>  [],
                'message'   =>  json_decode($validator->errors(), true, JSON_UNESCAPED_SLASHES),
            ];
            return response()->json($response, 400);
        }

        $jumlah = $request->jumlah;
        if ($user->saldo < $jumlah) {
            $response = [
                'status'    =>  'error',
                'data'      =>  [],
                'message'   =>  'Saldo anda tidak cukup'
            ];
            return response()->json($response, 400);
        }

        User::where('id', '=', $user->id)->update(['saldo' => $user->saldo - $jumlah]);
        Withdraw::create([
            'user'      =>  $user->id,
            'jumlah'    =>  $jumlah
        ]);

        $response = [
            'status'    =>  'success',
            'data'      =>  User::where('id', '=', $user->id)->first(),
            'message'   =>  'anda berhasil melakukan withdraw'
        ];
        return response()->json($response, 200);
    }

    public function transfer(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'email'     => 'required|email|exists:users,email',
            'jumlah'    =>  'required|integer',
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
        $jumlah = $request->jumlah;

        if ($user->saldo < $jumlah) {
            $response = [
                'status'    =>  'error',
                'data'      =>  [],
                'message'   =>  'Saldo anda tidak cukup'
            ];
            return response()->json($response, 400);
        }
        if ($email == $user->email) {
            $response = [
                'status'    =>  'error',
                'data'      =>  [],
                'message'   =>  'Email penerima tidak boleh sama dengan pengirim'
            ];
            return response()->json($response, 400);
        }

        $toUser = User::where('email', '=', $email)->first();
        User::where('id', '=', $user->id)->update(['saldo' => $user->saldo - $jumlah]);
        User::where('email', '=', $email)->update(['saldo' => $toUser->saldo + $jumlah]);
        Transfer::create([
            'from'      =>  $user->id,
            'to'        =>  $email,
            'jumlah'    =>  $jumlah
        ]);

        $response = [
            'status'    =>  'success',
            'data'      =>  User::where('id', '=', $user->id)->first(),
            'message'   =>  'anda berhasil melakukan transfer'
        ];
        return response()->json($response, 200);
    }

    public function mutasi()
    {
        $user       = JWTAuth::parseToken()->authenticate();
        $topup      = Topup::where('user', '=', $user->id)->get();
        $withdraw   = Withdraw::where('user', '=', $user->id)->get();
        $transfer   = Transfer::where('from', '=', $user->id)->get();
        $response = [
            'akun'      =>  $user,
            'topup'     =>  $topup,
            'withdraw'  =>  $withdraw,
            'transfer'  =>  $transfer
        ];
        return response()->json($response, 200);
    }
}
