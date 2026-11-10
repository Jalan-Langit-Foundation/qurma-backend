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
                $code => $p['name']
                    . ' - Rp' . number_format($p['price'], 0, ',', '.')
                    . ' (' . $p['weight_min'] . '–' . $p['weight_max'] . ' kg)',
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
                    ->live()
                    ->afterStateUpdated(fn ($state, $set) => $set(
                        'target_amount',
                        config("qurma.packages.$state.price")
                    ))
                    ->helperText('Loyalty khusus mudhohi QURMA 2026 (diverifikasi manual oleh admin).')
                    ->required(),

                TextInput::make('target_amount')
                    ->label('Target (Rp)')
                    ->numeric()
                    ->readOnly()
                    ->dehydrated(false)
                    ->helperText('Terisi otomatis dari paket, tidak bisa diketik.'),

                DatePicker::make('start_month')
                    ->label('Bulan mulai')
                    ->native(false)
                    ->displayFormat('F Y')
                    ->default(now()->startOfMonth())
                    ->dehydrateStateUsing(
                        fn ($state) => $state ? Carbon::parse($state)->startOfMonth()->toDateString() : null
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
                    ->helperText('Status Lunas diatur otomatis dari total setoran. Pakai Dibatalkan untuk membatalkan tabungan.')
                    ->required(),
            ]);
    }
}