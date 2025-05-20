<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ProductController;

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
// glocal middleware
Route::middleware([\App\Http\Utils\GlobalException::class]);
Route::get('/debug', function () {
   abort(500);
});
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Note routes - only accessible to users with 'note' role
    Route::middleware(\App\Http\Middleware\CheckRole::class . ':note')->group(function () {
        Route::apiResource('notes', NoteController::class);
    });

    // Product routes - only accessible to users with 'product' role
    Route::middleware(\App\Http\Middleware\CheckRole::class . ':product')->group(function () {
        Route::apiResource('products', ProductController::class);
    });
});
