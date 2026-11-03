<?php

namespace App\Filament\Resources\Fulfillments;

use App\Filament\Resources\Fulfillments\Pages\CreateFulfillment;
use App\Filament\Resources\Fulfillments\Pages\EditFulfillment;
use App\Filament\Resources\Fulfillments\Pages\ListFulfillments;
use App\Filament\Resources\Fulfillments\Schemas\FulfillmentForm;
use App\Filament\Resources\Fulfillments\Tables\FulfillmentsTable;
use App\Models\Fulfillment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FulfillmentResource extends Resource
{
    protected static ?string $modelLabel = 'Persiapan Qurban';

    protected static ?string $pluralModelLabel = 'Persiapan Qurban';

    protected static ?string $model = Fulfillment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return FulfillmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FulfillmentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFulfillments::route('/'),
            'create' => CreateFulfillment::route('/create'),
            'edit' => EditFulfillment::route('/{record}/edit'),
        ];
    }
}
