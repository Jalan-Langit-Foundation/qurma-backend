<?php

namespace App\Filament\Widgets;

use App\Models\Deposit;
use App\Models\Saving;
use App\Models\User;
use App\Services\SavingCalculator;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SavingStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $calc   = app(SavingCalculator::class);
        $counts = Saving::withSum('deposits', 'amount')->get()
            ->countBy(fn ($s) => $calc->statusFor($s, (int) $s->deposits_sum_amount));

        $overdue   = $counts->get('overdue', 0);
        $active    = $counts->get('active', 0) + $overdue;
        $completed = $counts->get('completed', 0);

        $monthSum = Deposit::whereBetween('paid_at', [
            now()->startOfMonth()->toDateString(),
            now()->endOfMonth()->toDateString(),
        ])->sum('amount');

        return [
            Stat::make('Mudhohi', User::where('role', 'user')->count())
                ->description('Penabung terdaftar'),

            Stat::make('Tabungan aktif', $active)
                ->description("{$completed} lunas · {$overdue} telat")
                ->color($overdue > 0 ? 'warning' : 'success'),

            Stat::make('Dana terkumpul', 'Rp' . number_format(Deposit::sum('amount'), 0, ',', '.'))
                ->description('Semua setoran tercatat'),

            Stat::make('Setoran bulan ini', 'Rp' . number_format($monthSum, 0, ',', '.'))
                ->description(now()->format('F Y')),
        ];
    }
}