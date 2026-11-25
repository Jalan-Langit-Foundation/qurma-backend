<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SavingCalculator;
use Illuminate\Http\JsonResponse;

class ProgramController extends Controller
{
    public function show(SavingCalculator $calc): JsonResponse
    {
        $settings = $calc->settings();

        $packages = collect(config('qurma.packages'))
            ->map(fn ($p, $code) => [
                'code'       => $code,
                'name'       => $p['name'],
                'price'      => $p['price'],
                'weight_min' => $p['weight_min'],
                'weight_max' => $p['weight_max'],
            ])
            ->sortBy('price')
            ->values()
            ->all();

        return response()->json([
            'success' => true,
            'message' => 'Data program',
            'data'    => [
                'program' => [
                    'year'                   => (int) config('qurma.program_year'),
                    'default_tenor'          => (int) config('qurma.default_tenor'),
                    'max_tenor'              => 12,
                    'rounding'               => (int) config('qurma.rounding'),
                    'registration_open'      => $calc->registrationOpen(),
                    'registration_deadline'  => $calc->registrationDeadline()->toDateString(),
                    'idul_adha_date'         => $calc->idulAdhaDate()->toDateString(),
                    'idul_adha_is_estimate'  => $settings->idul_adha_date === null,
                    'payoff_deadline'        => $calc->payoffDeadline()->toDateString(),
                    'last_installment_month' => $calc->lastInstallmentMonth(),
                    'packages'               => $packages,
                ],
            ],
        ]);
    }
}