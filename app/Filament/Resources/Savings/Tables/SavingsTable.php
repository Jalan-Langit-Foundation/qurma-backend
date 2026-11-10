<?php

namespace App\Filament\Resources\Savings\Tables;

use App\Models\Saving;
use App\Services\SavingCalculator;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SavingsTable
{
    public static function configure(Table $table): Table
    {
        $rupiah = fn ($state) => 'Rp' . number_format((int) $state, 0, ',', '.');

        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('user')->withSum('deposits', 'amount'))
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Penabung')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.phone')
                    ->label('WhatsApp')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('package_code')
                    ->label('Paket')
                    ->badge()
                    ->formatStateUsing(fn ($state) => config("qurma.packages.$state.name", $state)),

                TextColumn::make('target_amount')
                    ->label('Target')
                    ->formatStateUsing($rupiah)
                    ->sortable(),

                TextColumn::make('deposits_sum_amount')
                    ->label('Terkumpul')
                    ->formatStateUsing($rupiah)
                    ->sortable(),

                TextColumn::make('progress')
                    ->label('Progress')
                    ->state(fn ($record) => $record->target_amount > 0
                        ? min(100, intdiv(((int) $record->deposits_sum_amount) * 100, (int) $record->target_amount))
                        : 0)
                    ->suffix('%')
                    ->sortable(query: fn ($query, string $direction) => $query->orderByRaw(
                        'COALESCE(deposits_sum_amount, 0) / target_amount ' . ($direction === 'asc' ? 'asc' : 'desc')
                    )),

                TextColumn::make('state')
                    ->label('Status')
                    ->state(fn ($record) => app(SavingCalculator::class)
                        ->statusFor($record, (int) $record->deposits_sum_amount))
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'active'    => 'Aktif',
                        'overdue'   => 'Telat',
                        'completed' => 'Lunas',
                        'cancelled' => 'Dibatalkan',
                        default     => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'active'    => 'info',
                        'overdue'   => 'danger',
                        'completed' => 'success',
                        default     => 'gray',
                    }),

                TextColumn::make('start_month')
                    ->label('Mulai')
                    ->date('M Y'),
            ])
            ->filters([
                SelectFilter::make('package_code')
                    ->label('Paket')
                    ->options(collect(config('qurma.packages'))->map(fn ($p) => $p['name'])->all()),

                SelectFilter::make('program_year')
                    ->label('Tahun program')
                    ->options(fn () => Saving::query()->distinct()->orderBy('program_year')
                        ->pluck('program_year', 'program_year')->all()),

                SelectFilter::make('status')
                    ->label('Status tersimpan')
                    ->options([
                        'active'    => 'Aktif',
                        'completed' => 'Lunas',
                        'cancelled' => 'Dibatalkan',
                    ]),

                Filter::make('overdue')
                    ->label('Telat setoran')
                    ->toggle()
                    ->query(function ($query) {
                        $calc = app(SavingCalculator::class);

                        $ids = Saving::withSum('deposits', 'amount')->get()
                            ->filter(fn ($s) => $calc->statusFor($s, (int) $s->deposits_sum_amount) === 'overdue')
                            ->pluck('id');

                        return $query->whereIn('savings.id', $ids);
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}