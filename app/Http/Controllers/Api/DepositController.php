<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SavingCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function __construct(private SavingCalculator $calculator)
    {
    }

    public function index(Request $request, int $id): JsonResponse
    {
        // hanya tabungan milik user yang login
        $saving = $request->user()->savings()->find($id);

        if (! $saving) {
            return response()->json([
                'success' => false,
                'message' => 'Tabungan tidak ditemukan',
            ], 404);
        }

        $deposits  = $saving->deposits()->orderBy('paid_at')->orderBy('id')->get();
        $totalPaid = (int) $deposits->sum('amount');

        return response()->json([
            'success' => true,
            'message' => 'Riwayat setoran',
            'data'    => [
                'saving_id'     => $saving->id,
                'target_amount' => (int) $saving->target_amount,
                'total_paid'    => $totalPaid,
                'schedule'      => $this->calculator->schedule($saving, $totalPaid),
                'deposits'      => $deposits->map(fn ($d) => [
                    'id'      => $d->id,
                    'amount'  => (int) $d->amount,
                    'paid_at' => $d->paid_at->toDateString(),
                    'note'    => $d->note,
                ])->values(),
            ],
        ]);
    }
}