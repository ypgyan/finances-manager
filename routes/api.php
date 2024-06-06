<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
   return response()->json([
       'message' => 'Welcome to Finance Manager API',
       'version' => '0.0.1',
   ]);
});

// Authentication routes
Route::post('/signup', [AuthController::class, 'signUp'])->name('signUp');
Route::post('/signin', [AuthController::class, 'signIn'])->name('signIn');

Route::middleware('tenant')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:api');
});
