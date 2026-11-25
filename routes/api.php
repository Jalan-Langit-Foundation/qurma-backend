
<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DepositController;
use App\Http\Controllers\Api\FulfillmentController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SavingController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserDashboardController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ProgramController;
use Illuminate\Support\Facades\Route;

// Endpoint publik
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login'])->name('login');
Route::get('/program', [ProgramController::class, 'show']);

// Endpoint yang membutuhkan login
Route::middleware('auth:sanctum')->group(function () {

    // Autentikasi
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Profil pengguna
    Route::put('/user/profile', [ProfileController::class, 'update']);
    Route::put('/user/password', [ProfileController::class, 'updatePassword']);

    // Tabungan
    Route::post('/savings', [SavingController::class, 'store']);
    Route::get('/savings/current', [SavingController::class, 'current']);
    Route::get('/savings/{id}/deposits', [DepositController::class, 'index'])->whereNumber('id');

    // Persiapan dan laporan qurban
    Route::get('/savings/{id}/fulfillment', [FulfillmentController::class, 'show'])->whereNumber('id');
    Route::get('/savings/{id}/report', [ReportController::class, 'show'])->whereNumber('id');

    // Transaksi
    Route::get('/transactions', [TransactionController::class, 'index']);

    // Dashboard pengguna
    Route::get('/user/dashboard', [UserDashboardController::class, 'index']);
});
