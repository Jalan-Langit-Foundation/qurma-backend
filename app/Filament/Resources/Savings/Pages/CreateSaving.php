<?php

namespace App\Filament\Resources\Savings\Pages;

use App\Filament\Resources\Savings\SavingResource;
use App\Models\Saving;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateSaving extends CreateRecord
{
    protected static string $resource = SavingResource::class;

    // Satu penabung hanya boleh punya satu tabungan aktif (sama seperti aturan di API)
    protected function beforeCreate(): void
    {
        $userId = $this->data['user_id'] ?? null;

        if ($userId && Saving::where('user_id', $userId)->where('status', 'active')->exists()) {
            Notification::make()
                ->title('Penabung ini masih punya tabungan aktif')
                ->danger()
                ->send();

            $this->halt();
        }
    }

    // Target selalu diambil dari config paket, bukan dari ketikan
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['target_amount'] = config("qurma.packages.{$data['package_code']}.price");

        return $data;
    }
}