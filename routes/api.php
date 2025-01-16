<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Support\Facades\Hash;
use App\Models\Verification;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/register', function (Request $request) {
    if($request->API_TOKEN != env('API_TOKEN')){
        return response()->json(['status' => 'error', 'message' => 'Invalid token']);
    }
    $user = Worker::create([
        'username' => $request->username,
        'password' => Hash::make($request->password),
        'tg_id' => $request->tg_id,
        'tg_username' => $request->tg_username,
    ]);
    return response()->json(['status' => 'success']);
});


Route::get('update-verification', function () {
    $verifications = Verification::query()
        ->where('created_at', '>=', now()->subMinutes(2))
        ->get();
    foreach($verifications as $verification) {
        $verification->update(['verification_status' => 'completed']);
    }
    return response()->json(['message' => 'Verification updated']);
});
