<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function show(Request $request, int $id): JsonResponse
    {
        $saving = $request->user()->savings()
            ->with(['fulfillment', 'report.photos'])
            ->find($id);

        if (! $saving) {
            return response()->json([
                'success' => false,
                'message' => 'Tabungan tidak ditemukan',
            ], 404);
        }

        $report = $saving->report;

        $report = $saving->report;

        if (! $report || ! $report->isPublished()) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan belum tersedia',
            ], 404);
        }

        $fulfillment = $saving->fulfillment;

        $photos      = $report->photos->groupBy('type');

        $group = fn (string $type) => ($photos[$type] ?? collect())
            ->map(fn ($p) => [
                'id'  => $p->id,
                'url' => Storage::disk('public')->url($p->file_path),
            ])
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Shohibul report',
            'data'    => [
                'report' => [
                    'saving_id'      => $saving->id,
                    'program_year'   => $saving->program_year,
                    'shohibul_name'  => $request->user()->name,
                    'package'        => [
                        'code' => $saving->package_code,
                        'name' => config("qurma.packages.{$saving->package_code}.name", $saving->package_code),
                    ],
                    'animal_weight'  => $fulfillment?->animal_weight,
                    'location'       => $fulfillment?->location,
                    'slaughter_date' => $fulfillment?->slaughter_date?->toDateString(),
                    'notes'          => $report->notes,
                    'published_at'   => $report->published_at->toIso8601String(),
                    'photos'         => [
                        'animal'       => $group('animal'),
                        'slaughter'    => $group('slaughter'),
                        'distribution' => $group('distribution'),
                    ],
                ],
            ],
        ]);
    }
}