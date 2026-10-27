<?php

namespace App\Filament\Resources\Savings;

use App\Filament\Resources\Savings\Pages\CreateSaving;
use App\Filament\Resources\Savings\Pages\EditSaving;
use App\Filament\Resources\Savings\Pages\ListSavings;
use App\Models\Saving;
use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SavingResource extends Resource
{
    protected static ?string $model = Saving::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('user_id')
                ->label('Pilih User / Mudhohi')
                ->options(User::where('role', 'user')->pluck('name', 'id'))
                ->searchable()
                ->required(),

            Select::make('package_code')
                ->label('Paket Qurban')
                ->options([
                    'start' => 'Start (Rp 1.900.000 / 7 Bulan)',
                    'reguler' => 'Reguler', // Sesuaikan dengan config qurma.php kamu kalau ada
                ])
                ->required(),

            TextInput::make('tenor_months')
                ->label('Tenor (Bulan)')
                ->numeric()
                ->default(7)
                ->required(),

            DatePicker::make('start_month')
                ->label('Bulan Mulai')
                ->required()
                ->default(now()),

            TextInput::make('program_year')
                ->label('Tahun Program')
                ->numeric()
                ->default(2027)
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('user.name')->label('Nama Mudhohi')->searchable(),
            TextColumn::make('package_code')->label('Paket')->badge(),
            TextColumn::make('target_amount')->label('Target')->money('IDR', true),
            TextColumn::make('status')->label('Status')->badge(),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DepositsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSavings::route('/'),
            'create' => CreateSaving::route('/create'),
            'edit' => EditSaving::route('/{record}/edit'),
        ];
    }
}