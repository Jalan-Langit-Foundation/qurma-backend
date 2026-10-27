<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSavingRequest;
use App\Services\SavingCalculator;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SavingController extends Controller
{
    public function __construct(private SavingCalculator $calculator)
    {
    }

    public function store(StoreSavingRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($user->savings()->where('status', 'active')->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu masih memiliki tabungan yang aktif',
            ], 409);
        }

        $data = $request->validated();

        $saving = $user->savings()->create([
            'package_code'  => $data['package_code'],
            'target_amount' => config("qurma.packages.{$data['package_code']}.price"),
            'tenor_months'  => $data['tenor_months'] ?? config('qurma.default_tenor'),
            'start_month'   => Carbon::createFromFormat('!Y-m', $data['start_month'])->toDateString(),
            'program_year'  => config('qurma.program_year'),
            'status'        => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tabungan berhasil dibuat',
            'data'    => ['saving' => $this->calculator->summary($saving)],
        ], 201);
    }

    public function current(Request $request): JsonResponse
    {
        $user = $request->user();

        $saving = $user->savings()->where('status', 'active')->latest('id')->first()
            ?? $user->savings()->latest('id')->first();

        if (! $saving) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada tabungan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data tabungan',
            'data'    => ['saving' => $this->calculator->summary($saving)],
        ]);
    }
}