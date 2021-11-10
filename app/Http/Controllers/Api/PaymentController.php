<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Topup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class PaymentController extends Controller
{
    public function topUp(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'saldo'     => 'required|integer',
        ]);

        if ($validator->fails()) {
            $response = [
                'status'    =>  'error',
                'data'      =>  [],
                'message'   =>  json_decode($validator->errors(), true, JSON_UNESCAPED_SLASHES),
            ];
            return response()->json($response, 400);
        }
        $saldo = $request->saldo;
        User::where('id', '=', $user->id)->update(['saldo' => $user->saldo + $saldo]);

        Topup::create([
            'user'      =>  $user->id,
            'jumlah'    =>  $saldo
        ]);

        $response = [
            'status'    =>  'success',
            'data'      =>  User::where('id', '=', $user->id)->first(),
            'message'   =>  'anda berhasil menambahkan saldo'
        ];
        return response()->json($response, 200);
    }

    public function withDraw()
    {
    }

    public function transfer()
    {
    }

    public function mutasi()
    {
    }
}
