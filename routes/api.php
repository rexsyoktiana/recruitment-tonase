<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\LogicTestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('container/{id}', [LogicTestController::class, 'index']);
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('reset', [AuthController::class, 'reset']);
Route::post('reset/{token}', [AuthController::class, 'changePassword']);
Route::get('user', [AuthController::class, 'user']);


Route::post('topup', [PaymentController::class, 'topUp'])->middleware('jwt.verify');
Route::post('withdraw', [PaymentController::class, 'withdraw'])->middleware('jwt.verify');
Route::post('transfer', [PaymentController::class, 'transfer'])->middleware('jwt.verify');
