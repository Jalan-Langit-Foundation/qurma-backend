<?php

namespace App\Filament\Resources\Fulfillments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FulfillmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // UBAH DI SINI: dari saving.user jadi parentSaving.user
            ->modifyQueryUsing(fn ($query) => $query->with('parentSaving.user'))
            ->columns([
                // UBAH DI SINI: dari saving.user.name jadi parentSaving.user.name
                TextColumn::make('parentSaving.user.name')
                    ->label('Penabung')
                    ->searchable(),

                // UBAH DI SINI: dari saving.package_code jadi parentSaving.package_code
                TextColumn::make('parentSaving.package_code')
                    ->label('Paket')
                    ->formatStateUsing(fn ($state) => config("qurma.packages.$state.name", $state))
                    ->badge(),

                TextColumn::make('status')
                    ->label('Tahap')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'animal_preparing'  => 'Domba disiapkan',
                        'verifying'         => 'Verifikasi',
                        'waiting_execution' => 'Menunggu hari H',
                        'executed'          => 'Dilaksanakan',
                        default             => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'executed'          => 'success',
                        'waiting_execution' => 'warning',
                        default             => 'info',
                    }),

                TextColumn::make('animal_weight')
                    ->label('Bobot')
                    ->suffix(' kg'),

                TextColumn::make('slaughter_date')
                    ->label('Tanggal')
                    ->date('d M Y'),

                TextColumn::make('vendor_name')
                    ->label('Vendor'),
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