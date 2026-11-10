<?php

namespace App\Filament\Widgets;

use App\Models\Saving;
use App\Services\SavingCalculator;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class DueSavings extends TableWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $ids = Saving::withSum('deposits', 'amount')
            ->where('status', '!=', 'cancelled')
            ->get()
            ->filter(fn ($s) => $this->dueRow($s) !== null)
            ->pluck('id');

        return $table
            ->heading('Setoran jatuh tempo & telat')
            ->query(
                Saving::query()
                    ->with('user')
                    ->withSum('deposits', 'amount')
                    ->whereIn('id', $ids)
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label('Penabung'),

                TextColumn::make('user.phone')
                    ->label('WhatsApp')
                    ->color('success')
                    ->url(function ($record) {
                        $row  = $this->dueRow($record);
                        $paid = (int) $record->deposits_sum_amount;
                        $pct  = $record->target_amount > 0
                            ? min(100, intdiv($paid * 100, (int) $record->target_amount))
                            : 0;

                        $text = "Assalamu'alaikum {$record->user->name}.\n"
                            . "Tabungan qurbanmu sudah memasuki jadwal setoran bulan ini.\n"
                            . 'Target: Rp' . number_format($record->target_amount, 0, ',', '.') . "\n"
                            . 'Terkumpul: Rp' . number_format($paid, 0, ',', '.') . "\n"
                            . "Progress: {$pct}%\n"
                            . 'Setoran bulan ini: Rp' . number_format($row['remaining_amount'] ?? 0, 0, ',', '.') . "\n"
                            . 'Yuk lanjutkan sedikit demi sedikit.';

                        return 'https://wa.me/' . preg_replace('/^0/', '62', $record->user->phone)
                            . '?text=' . rawurlencode($text);
                    }, true),

                TextColumn::make('package_code')
                    ->label('Paket')
                    ->badge()
                    ->formatStateUsing(fn ($state) => config("qurma.packages.$state.name", $state)),

                TextColumn::make('due_month')
                    ->label('Bulan')
                    ->state(fn ($record) => Carbon::createFromFormat(
                        '!Y-m',
                        $this->dueRow($record)['month'] ?? now()->format('Y-m')
                    )->format('M Y')),

                TextColumn::make('due_amount')
                    ->label('Kekurangan')
                    ->state(fn ($record) => 'Rp' . number_format($this->dueRow($record)['remaining_amount'] ?? 0, 0, ',', '.')),

                TextColumn::make('due_status')
                    ->label('Status')
                    ->state(fn ($record) => ($this->dueRow($record)['status'] ?? '') === 'overdue' ? 'Telat' : 'Jatuh tempo')
                    ->badge()
                    ->color(fn ($state) => $state === 'Telat' ? 'danger' : 'warning'),
            ])
            ->paginated(false)
            ->emptyStateHeading('Tidak ada setoran yang jatuh tempo atau telat');
    }

    /** Cicilan pertama yang belum lunas dan bulannya sudah tiba atau lewat. */
    private function dueRow(Saving $saving): ?array
    {
        $paid = (int) $saving->deposits_sum_amount;

        return collect(app(SavingCalculator::class)->schedule($saving, $paid))
            ->first(fn ($row) => $row['remaining_amount'] > 0 && $row['month'] <= now()->format('Y-m'));
    }
}