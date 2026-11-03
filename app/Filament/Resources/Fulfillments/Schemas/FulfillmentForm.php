<?php

namespace App\Filament\Resources\Fulfillments\Schemas;

use App\Models\Saving;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FulfillmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('saving_id')
                    ->label('Tabungan (sudah lunas)')
                    ->options(fn () => Saving::with('user')
                        ->where('status', 'completed')
                        ->get()
                        ->mapWithKeys(fn ($s) => [
                            $s->id => $s->user->name . ' - '
                                . config("qurma.packages.{$s->package_code}.name", $s->package_code)
                                . ' (#' . $s->id . ')',
                        ]))
                    ->searchable()
                    ->unique(ignoreRecord: true)
                    ->disabledOn('edit')
                    ->required(),

                Select::make('status')
                    ->label('Tahap persiapan')
                    ->options([
                        'animal_preparing'  => 'Domba sedang disiapkan',
                        'verifying'         => 'Verifikasi spesifikasi',
                        'waiting_execution' => 'Menunggu hari pelaksanaan',
                        'executed'          => 'Qurban sudah dilaksanakan',
                    ])
                    ->default('animal_preparing')
                    ->required(),

                TextInput::make('vendor_name')
                    ->label('Vendor / mitra (tidak tampil ke user)')
                    ->maxLength(255),

                TextInput::make('animal_weight')
                    ->label('Bobot domba')
                    ->numeric()
                    ->suffix('kg'),

                DatePicker::make('slaughter_date')
                    ->label('Tanggal pelaksanaan')
                    ->native(false),

                TextInput::make('location')
                    ->label('Lokasi pelaksanaan')
                    ->maxLength(255),
            ]);
    }
}