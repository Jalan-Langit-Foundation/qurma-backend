<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SavingCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function __construct(private SavingCalculator $calculator)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Ambil semua tabungan aktif milik user
        $savings = $user->savings()->with(['fulfillment', 'report'])->get();

        $summaries = $savings->map(fn ($saving) => $savingCalculatorSummary = $this->calculator->summary($saving));

        return response()->json([
            'success' => true,
            'message' => 'Dashboard user berhasil dimuat',
            'data' => [
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'active_savings_count' => $savings->count(),
                'savings_summaries' => $summaries,
            ],
        ]);
    }
}