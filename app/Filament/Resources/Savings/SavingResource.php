<?php

namespace App\Filament\Resources\Savings;

use App\Filament\Resources\Savings\Pages\CreateSaving;
use App\Filament\Resources\Savings\Pages\EditSaving;
use App\Filament\Resources\Savings\Pages\ListSavings;
use App\Filament\Resources\Savings\RelationManagers\DepositsRelationManager;
use App\Filament\Resources\Savings\Schemas\SavingForm;
use App\Filament\Resources\Savings\Tables\SavingsTable;
use App\Models\Saving;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SavingResource extends Resource
{
    protected static ?string $model = Saving::class;

    protected static ?string $modelLabel = 'Tabungan';

    protected static ?string $pluralModelLabel = 'Tabungan';

    protected static ?string $navigationLabel = 'Tabungan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return SavingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SavingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            DepositsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListSavings::route('/'),
            'create' => CreateSaving::route('/create'),
            'edit'   => EditSaving::route('/{record}/edit'),
        ];
    }
}