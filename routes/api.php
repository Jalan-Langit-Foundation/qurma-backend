<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SavingController;


// Endpoint Publik
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login'])->name('login'); // <--- TAMBAHKAN ->name('login') DI SINI!

// Endpoint Protected (Butuh Login)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('savings', [SavingController::class, 'store']);
    Route::get('savings/current', [SavingController::class, 'current']);
});
