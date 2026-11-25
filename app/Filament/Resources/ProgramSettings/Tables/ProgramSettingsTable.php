<?php

namespace App\Filament\Resources\ProgramSettings\Tables;

use App\Services\SavingCalculator;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProgramSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('idul_adha_date')
                    ->label('Idul Adha (resmi)')
                    ->date('d M Y')
                    ->placeholder('Belum ditetapkan (pakai perkiraan)'),

                TextColumn::make('payoff_lead_days')
                    ->label('Batas pelunasan')
                    ->prefix('H-'),

                TextColumn::make('payoff')
                    ->label('Tanggal batas pelunasan')
                    ->state(fn () => app(SavingCalculator::class)->payoffDeadline()->format('d M Y')),

                TextColumn::make('registration_deadline')
                    ->label('Batas pendaftaran')
                    ->date('d M Y'),

                IconColumn::make('last_month_in_deadline')
                    ->label('Cicilan terakhir di bulan batas')
                    ->boolean(),
            ])
            ->paginated(false)
            ->recordActions([
                EditAction::make(),
            ]);
    }
}