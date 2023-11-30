<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Me\ProfileController;

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
Route::post('/sign-up', [AuthController::class, 'signUp']);
Route::post('/sign-in', [AuthController::class, 'signIn']);

Route::middleware('auth:api')->group(function() {
    // Route::get('/auth-only', [AuthController::class, function() { return response()->json(['data' => 'oy']); }]);
    Route::prefix('/me')->group(function() {
        Route::get('/profile', [ProfileController::class, 'show']);
    });
    Route::post('/sign-out', [AuthController::class, 'signOut']);
});

Route::middleware('jwt.refresh')->post('/refresh', [AuthController::class, 'refresh']);
