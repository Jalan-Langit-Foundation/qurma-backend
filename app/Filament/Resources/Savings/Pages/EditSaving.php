<?php

namespace App\Filament\Resources\Savings\Pages;

use App\Filament\Resources\Savings\SavingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSaving extends EditRecord
{
    protected static string $resource = SavingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    // Kalau paket diganti (upgrade), target ikut berubah. Kalau tidak, target lama dipertahankan.
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['package_code'] ?? null) !== $this->record->package_code) {
            $data['target_amount'] = config("qurma.packages.{$data['package_code']}.price");
        }

        return $data;
    }

    // Status aktif/lunas dihitung ulang karena target bisa berubah
    protected function afterSave(): void
    {
        $this->record->refreshStatus();
    }
}