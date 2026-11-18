<?php

namespace App\Filament\Resources\Savings\Pages;

use App\Filament\Resources\Savings\SavingResource;
use App\Models\Saving;
use App\Services\SavingCalculator;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateSaving extends CreateRecord
{
    protected static string $resource = SavingResource::class;

    // Aturan yang sama dengan API: satu tabungan aktif per penabung dan batas pelunasan program
    protected function beforeCreate(): void
    {
        $calc   = app(SavingCalculator::class);
        $start  = Carbon::parse($this->data['start_month'])->format('Y-m');
        $tenor  = (int) ($this->data['tenor_months'] ?? config('qurma.default_tenor'));
        $userId = $this->data['user_id'] ?? null;

        $error = ($userId && Saving::where('user_id', $userId)->where('status', 'active')->exists())
            ? 'Penabung ini masih punya tabungan aktif'
            : ($calc->startMonthError($start) ?? $calc->tenorError($start, $tenor));

        if ($error) {
            Notification::make()->title($error)->danger()->send();

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