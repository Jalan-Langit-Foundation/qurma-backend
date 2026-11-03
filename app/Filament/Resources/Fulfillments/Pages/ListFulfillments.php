<?php

namespace App\Filament\Resources\Fulfillments\Pages;

use App\Filament\Resources\Fulfillments\FulfillmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFulfillments extends ListRecords
{
    protected static string $resource = FulfillmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
