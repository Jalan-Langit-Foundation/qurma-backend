<?php

namespace App\Filament\Resources\Savings\Pages;

use App\Filament\Resources\Savings\SavingResource;
use App\Services\SavingCalculator;
use Carbon\Carbon;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
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

    // Batas pelunasan hanya dicek kalau bulan mulai atau tenor diubah
    protected function beforeSave(): void
    {
        $calc  = app(SavingCalculator::class);
        $start = Carbon::parse($this->data['start_month'])->format('Y-m');
        $tenor = (int) $this->data['tenor_months'];

        if ($start === $this->record->start_month->format('Y-m') && $tenor === (int) $this->record->tenor_months) {
            return;
        }

        $error = $calc->startMonthError($start) ?? $calc->tenorError($start, $tenor);

        if ($error) {
            Notification::make()->title($error)->danger()->send();

            $this->halt();
        }
    }

    // Kalau paket diganti (upgrade), target ikut berubah
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