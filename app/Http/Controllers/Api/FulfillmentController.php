<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SavingCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FulfillmentController extends Controller
{
    private const LEVEL = [
        'animal_preparing'  => 0,
        'verifying'         => 1,
        'waiting_execution' => 2,
        'executed'          => 3,
    ];

    public function __construct(private SavingCalculator $calculator)
    {
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $saving = $request->user()->savings()->with(['fulfillment', 'report'])->find($id);

        if (! $saving) {
            return response()->json([
                'success' => false,
                'message' => 'Tabungan tidak ditemukan',
            ], 404);
        }

        $funded = $this->calculator->summary($saving)['status'] === 'completed';

        // Dana belum tercapai: tetap 200, tapi available = false
        if (! $funded) {
            return $this->respond([
                'saving_id' => $saving->id,
                'available' => false,
                'status'    => null,
                'steps'     => collect(['funds', 'animal', 'verification', 'execution', 'report'])
                    ->map(fn ($key) => ['key' => $key, 'state' => 'pending'])
                    ->all(),
                'details'   => ['animal_weight' => null, 'slaughter_date' => null, 'location' => null],
            ]);
        }

        $fulfillment = $saving->fulfillment;
        $status      = $fulfillment?->status ?? 'animal_preparing';
        $level       = self::LEVEL[$status] ?? 0;
        $published = $saving->report?->isPublished() ?? false;

        return $this->respond([
            'saving_id' => $saving->id,
            'available' => true,
            'status'    => $status,
            'steps'     => [
                ['key' => 'funds',        'state' => 'done'],
                ['key' => 'animal',       'state' => $level >= 1 ? 'done' : 'in_progress'],
                ['key' => 'verification', 'state' => $level >= 2 ? 'done' : ($level === 1 ? 'in_progress' : 'pending')],
                ['key' => 'execution',    'state' => $level >= 3 ? 'done' : ($level === 2 ? 'in_progress' : 'pending')],
                ['key' => 'report',       'state' => $published ? 'done' : ($level >= 3 ? 'in_progress' : 'pending')],
            ],
            'details'   => [
                'animal_weight'  => $fulfillment?->animal_weight,
                'slaughter_date' => $fulfillment?->slaughter_date?->toDateString(),
                'location'       => $fulfillment?->location,
            ],
        ]);
    }

    private function respond(array $tracking): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Tracking persiapan qurban',
            'data'    => ['tracking' => $tracking],
        ]);
    }
}