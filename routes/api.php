<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SavingController;
use App\Http\Controllers\Api\DepositController;

// Endpoint Publik
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login'])->name('login');

// Endpoint Protected (Butuh Login)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    
    // Tabungan & Setoran
    Route::post('savings', [SavingController::class, 'store']);
    Route::get('savings/current', [SavingController::class, 'current']);
    Route::get('savings/{id}/deposits', [DepositController::class, 'index'])->whereNumber('id');
});