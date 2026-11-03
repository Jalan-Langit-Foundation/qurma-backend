<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SavingController;
use App\Http\Controllers\Api\DepositController;
use App\Http\Controllers\Api\FulfillmentController;
use App\Http\Controllers\Api\ReportController;

// Endpoint Publik
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login'])->name('login');

// Endpoint Protected (Butuh Login)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    
    // Tabungan, Setoran, & Tracking
    Route::post('savings', [SavingController::class, 'store']);
    Route::get('savings/current', [SavingController::class, 'current']);
    Route::get('savings/{id}/deposits', [DepositController::class, 'index'])->whereNumber('id');
    Route::get('savings/{id}/fulfillment', [FulfillmentController::class, 'show'])->whereNumber('id');
    
    // Shohibul Report (Masuk ke dalam auth agar $request->user() aman)
    Route::get('savings/{id}/report', [ReportController::class, 'show'])->whereNumber('id');
});