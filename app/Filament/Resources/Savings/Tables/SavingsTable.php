<?php

namespace App\Filament\Resources\Savings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SavingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('user')->withSum('deposits', 'amount'))
            ->columns([
                TextColumn::make('user.name')
                    ->label('Penabung')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('package_code')
                    ->label('Paket')
                    ->formatStateUsing(fn ($state) => config("qurma.packages.$state.name", $state))
                    ->badge(),

                TextColumn::make('target_amount')
                    ->label('Target')
                    ->formatStateUsing(fn ($state) => 'Rp' . number_format($state, 0, ',', '.')),

                TextColumn::make('total_paid')
                    ->label('Terkumpul')
                    ->state(fn ($record) => (int) $record->deposits_sum_amount)
                    ->formatStateUsing(fn ($state) => 'Rp' . number_format($state, 0, ',', '.')),

                TextColumn::make('progress')
                    ->label('Progress')
                    ->state(fn ($record) => $record->target_amount > 0
                        ? min(100, (int) floor(((int) $record->deposits_sum_amount) / $record->target_amount * 100))
                        : 0)
                    ->suffix('%'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'active'    => 'Aktif',
                        'completed' => 'Lunas',
                        'cancelled' => 'Dibatalkan',
                        default     => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'active'    => 'info',
                        'completed' => 'success',
                        default     => 'gray',
                    }),

                TextColumn::make('start_month')
                    ->label('Mulai')
                    ->date('M Y'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active'    => 'Aktif',
                        'completed' => 'Lunas',
                        'cancelled' => 'Dibatalkan',
                    ]),
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