<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = $request->user()->savings()
            ->with('deposits')
            ->get()
            ->flatMap(fn ($saving) => $saving->deposits);

        // Optional filtering by status or sorting if needed
        $sorted = $query->sortByDesc('created_at')->values();

        return response()->json([
            'success' => true,
            'message' => 'Daftar transaksi berhasil dimuat',
            'data' => [
                'transactions' => $sorted,
            ],
        ]);
    }
}