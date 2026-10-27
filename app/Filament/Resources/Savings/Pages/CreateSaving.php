<?php

namespace App\Filament\Resources\Savings\Pages;

use App\Filament\Resources\Savings\SavingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSaving extends CreateRecord
{
    protected static string $resource = SavingResource::class;

    // Tambahkan method ini agar target_amount otomatis terambil dari config paket
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['target_amount'] = config("qurma.packages.{$data['package_code']}.price");
        return $data;
    }
}