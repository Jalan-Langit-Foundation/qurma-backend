<?php

namespace App\Filament\Resources\Savings\Schemas;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class SavingForm
{
    public static function configure(Schema $schema): Schema
    {
        $packages = collect(config('qurma.packages'))
            ->mapWithKeys(fn ($p, $code) => [
                $code => $p['name'] . ' - Rp' . number_format($p['price'], 0, ',', '.'),
            ])
            ->all();

        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Penabung')
                    ->relationship(
                        'user',
                        'name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('role', 'user'),
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('package_code')
                    ->label('Paket')
                    ->options($packages)
                    ->required(),

                TextInput::make('target_amount')
                    ->label('Target (Rp)')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(false)
                    ->hiddenOn('create'),

                DatePicker::make('start_month')
    ->label('Bulan Mulai')
    ->native(false)
    ->displayFormat('F Y')
    ->dehydrateStateUsing(
        fn ($state) => $state ? \Carbon\Carbon::parse($state)->startOfMonth()->toDateString() : null
    )
    ->required(),

                TextInput::make('tenor_months')
                    ->label('Tenor (bulan)')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(12)
                    ->default(config('qurma.default_tenor'))
                    ->required(),

                TextInput::make('program_year')
                    ->label('Tahun program')
                    ->numeric()
                    ->default(config('qurma.program_year'))
                    ->required(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active'    => 'Aktif',
                        'completed' => 'Lunas',
                        'cancelled' => 'Dibatalkan',
                    ])
                    ->default('active')
                    ->required(),
            ]);
    }
}