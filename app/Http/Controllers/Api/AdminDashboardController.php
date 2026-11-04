<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Fulfillment;
use App\Models\Saving;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function stats(Request $request): JsonResponse
    {
        // Pastikan hanya admin (bisa disesuaikan middleware-nya)
        $totalSavings = Saving::count();
        $totalTarget = Saving::sum('target_amount');
        $totalCollected = Deposit::sum('amount');
        $readyToExecute = Fulfillment::where('status', 'waiting_execution')->count();
        $executed = Fulfillment::where('status', 'executed')->count();

        return response()->json([
            'success' => true,
            'message' => 'Statistik dashboard admin berhasil dimuat',
            'data' => [
                'stats' => [
                    'total_savings' => $totalSavings,
                    'total_target_amount' => (int) $totalTarget,
                    'total_collected_amount' => (int) $totalCollected,
                    'animals_waiting_execution' => $readyToExecute,
                    'animals_executed' => $executed,
                ],
            ],
        ]);
    }
}