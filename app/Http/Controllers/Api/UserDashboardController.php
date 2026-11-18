<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Saving;
use App\Services\SavingCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class UserDashboardController extends Controller
{
    public function __construct(private SavingCalculator $calculator)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Tabungan aktif dulu, kalau tidak ada pakai yang terakhir
        $saving = $user->savings()->where('status', 'active')->latest('id')->first()
            ?? $user->savings()->latest('id')->first();

        return response()->json([
            'success' => true,
            'message' => 'Dashboard user',
            'data'    => [
                'user'          => ['name' => $user->name, 'email' => $user->email],
                'savings_count' => $user->savings()->count(),
                'has_saving'    => $saving !== null,
                'saving'        => $saving ? $this->calculator->summary($saving) : null,
                'insights'      => $saving ? $this->insights($saving) : null,
            ],
        ]);
    }

    /** Informasi turunan yang dihitung dari data tabungan dan setoran. */
    private function insights(Saving $saving): array
    {
        $target   = (int) $saving->target_amount;
        $paid     = (int) $saving->deposits()->sum('amount');
        $monthly  = $this->calculator->monthlyAmount($target, (int) $saving->tenor_months);
        $schedule = collect($this->calculator->schedule($saving, $paid));
        $current  = now()->format('Y-m');
        $progress = $target > 0 ? intdiv($paid * 100, $target) : 0;

        // 1. Ringkasan bulan ini
        $deposited = (int) $saving->deposits()
            ->whereBetween('paid_at', [
                now()->startOfMonth()->toDateString(),
                now()->endOfMonth()->toDateString(),
            ])
            ->sum('amount');

        $row = $schedule->firstWhere('month', $current);

        // 3. Posisi terhadap jadwal
        $expectedPast    = (int) $schedule->filter(fn ($r) => $r['month'] < $current)->sum('amount');
        $expectedThrough = (int) $schedule->filter(fn ($r) => $r['month'] <= $current)->sum('amount');

        $status      = 'on_track';
        $amountBehind = 0;
        $monthsAhead  = 0;

        if ($paid >= $target) {
            $status = 'completed';
        } elseif ($schedule->first()['month'] > $current) {
            $status = 'not_started';
        } elseif ($paid < $expectedPast) {
            $status       = 'behind';
            $amountBehind = $expectedPast - $paid;
        } elseif ($monthly > 0 && $paid >= $expectedThrough + $monthly) {
            $status      = 'ahead';
            $monthsAhead = intdiv($paid - $expectedThrough, $monthly);
        }

        // 6. Saran upgrade ke paket di atasnya (hanya tampil bila progres >= 50%)
        $upgrade = null;
        $packages = collect(config('qurma.packages'))->sortBy('price');
        $higher   = $packages->filter(fn ($p) => $p['price'] > $target);
        $nextCode = $higher->keys()->first();

        if ($nextCode !== null && $paid < $target && $progress >= 50 && $saving->status !== 'cancelled') {
            $current_pkg = config("qurma.packages.{$saving->package_code}");
            $next        = $higher->first();

            $upgrade = [
                'from' => [
                    'code'  => $saving->package_code,
                    'name'  => $current_pkg['name'] ?? $saving->package_code,
                    'price' => $target,
                ],
                'to' => [
                    'code'  => $nextCode,
                    'name'  => $next['name'],
                    'price' => $next['price'],
                ],
                'additional_amount' => $next['price'] - $target,
            ];
        }

        return [
            'month_summary' => [
                'month'       => $current,
                'installment' => $row
                    ? Arr::only($row, ['amount', 'paid_amount', 'remaining_amount', 'status'])
                    : null,
                'deposited'   => $deposited,
            ],
            // 2. "Sehari cuma ..." (dari setoran bulanan dibagi 30 hari)
            'daily_estimate'    => (int) round($monthly / 30),
            'schedule_position' => [
                'status'        => $status,
                'amount_behind' => $amountBehind,
                'months_ahead'  => $monthsAhead,
            ],
            'upgrade_suggestion' => $upgrade,
        ];
    }
}