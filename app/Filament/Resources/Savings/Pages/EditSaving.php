<?php

namespace App\Filament\Resources\Savings\Pages;

use App\Filament\Resources\Savings\SavingResource;
use Filament\Actions\DeleteAction; // <--- Import DeleteAction
use Filament\Resources\Pages\EditRecord;

class EditSaving extends EditRecord
{
    protected static string $resource = SavingResource::class;

    // Memunculkan kembali tombol Delete di pojok kanan atas halaman Edit
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}