<?php

namespace App\Services;

use App\Models\Saving;
use Carbon\Carbon;

class SavingCalculator
{
    public function monthlyAmount(int $target, int $tenor): int
    {
        $rounding = (int) config('qurma.rounding', 5000);

        return (int) (ceil($target / $tenor / $rounding) * $rounding);
    }

    public function lastInstallment(int $target, int $tenor): int
    {
        return max(0, $target - $this->monthlyAmount($target, $tenor) * ($tenor - 1));
    }

    /** Jadwal setoran per bulan, total setoran dialokasikan berurutan dari cicilan pertama. */
    public function schedule(Saving $saving, int $totalPaid): array
    {
        $target  = (int) $saving->target_amount;
        $tenor   = (int) $saving->tenor_months;
        $monthly = $this->monthlyAmount($target, $tenor);
        $last    = $this->lastInstallment($target, $tenor);
        $start   = Carbon::parse($saving->start_month)->startOfMonth();
        $current = now()->format('Y-m');

        $rows = [];
        $left = $totalPaid;
        $nextAssigned = false;

        for ($i = 1; $i <= $tenor; $i++) {
            $amount    = $i === $tenor ? $last : $monthly;
            $paid      = min($amount, $left);
            $left     -= $paid;
            $remaining = $amount - $paid;
            $month     = $start->copy()->addMonthsNoOverflow($i - 1)->format('Y-m');

            if ($remaining === 0) {
                $status = 'paid';
            } elseif ($month < $current) {
                $status = 'overdue';
            } elseif (! $nextAssigned) {
                $status = 'next';
                $nextAssigned = true;
            } else {
                $status = 'scheduled';
            }

            $rows[] = [
                'installment_no'   => $i,
                'month'            => $month,
                'amount'           => $amount,
                'paid_amount'      => $paid,
                'remaining_amount' => $remaining,
                'status'           => $status,
                'is_final'         => $i === $tenor,
            ];
        }

        return $rows;
    }

    /** Data tabungan untuk response API (dashboard). */
    public function summary(Saving $saving): array
    {
        $saving->loadMissing(['fulfillment', 'report']);

        $target    = (int) $saving->target_amount;
        $tenor     = (int) $saving->tenor_months;
        $totalPaid = (int) $saving->deposits()->sum('amount');
        $schedule  = collect($this->schedule($saving, $totalPaid));
        $nextRow   = $schedule->first(fn ($row) => $row['remaining_amount'] > 0);
        $progress  = $target > 0 ? min(100, intdiv($totalPaid * 100, $target)) : 0;

        $status = match (true) {
            $saving->status === 'cancelled'      => 'cancelled',
            $totalPaid >= $target                => 'completed',
            $schedule->contains('status', 'overdue') => 'overdue',
            default                              => 'active',
        };

        $fulfillment = $saving->fulfillment?->status;

        return [
            'id'      => $saving->id,
            'package' => [
                'code' => $saving->package_code,
                'name' => config("qurma.packages.{$saving->package_code}.name", $saving->package_code),
            ],
            'program_year'            => $saving->program_year,
            'status'                  => $status,
            'target_amount'           => $target,
            'total_paid'              => $totalPaid,
            'remaining_amount'        => max(0, $target - $totalPaid),
            'progress_percent'        => $progress,
            'tenor_months'            => $tenor,
            'start_month'             => Carbon::parse($saving->start_month)->format('Y-m'),
            'end_month'               => $schedule->last()['month'],
            'monthly_amount'          => $this->monthlyAmount($target, $tenor),
            'last_installment_amount' => $this->lastInstallment($target, $tenor),
            'next_installment'        => $nextRow
                ? ['month' => $nextRow['month'], 'amount' => $nextRow['remaining_amount']]
                : null,
            'journey' => [
                'target_selected' => true,
                'saving_started'  => true,
                'half_reached'    => $progress >= 50,
                'target_reached'  => $totalPaid >= $target,
                'animal_prepared' => in_array($fulfillment, ['verifying', 'waiting_execution', 'executed'], true),
                'qurban_done'     => $fulfillment === 'executed',
                'report_received' => $saving->report?->published_at !== null,
            ],
        ];
    }
}