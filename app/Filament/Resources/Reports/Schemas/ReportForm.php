<?php

namespace App\Filament\Resources\Reports\Schemas;

use App\Models\Saving;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('saving_id')
                    ->label('Tabungan (qurban sudah dilaksanakan)')
                    ->options(fn () => Saving::with('user')
                        ->whereHas('fulfillment', fn ($q) => $q->where('status', 'executed'))
                        ->get()
                        ->mapWithKeys(fn ($s) => [
                            $s->id => $s->user->name . ' - '
                                . config("qurma.packages.{$s->package_code}.name", $s->package_code)
                                . ' (#' . $s->id . ')',
                        ]))
                    ->searchable()
                    ->unique(ignoreRecord: true)
                    ->disabledOn('edit')
                    ->required(),

                Textarea::make('notes')
                    ->label('Catatan laporan')
                    ->rows(4)
                    ->columnSpanFull(),

                DateTimePicker::make('published_at')
                    ->label('Dipublikasikan pada')
                    ->helperText('Kosongkan kalau belum ingin ditampilkan ke penabung.')
                    ->native(false),
            ]);
    }
}