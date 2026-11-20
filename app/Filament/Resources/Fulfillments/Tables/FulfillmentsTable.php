<?php

namespace App\Filament\Resources\Fulfillments\Tables;

use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class FulfillmentsTable
{
    public const STAGES = [
        'animal_preparing'  => 'Domba disiapkan',
        'verifying'         => 'Verifikasi',
        'waiting_execution' => 'Menunggu hari H',
        'executed'          => 'Dilaksanakan',
    ];

    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('parentSaving.user'))
            ->defaultSort('id', 'desc')
            ->paginationPageOptions([10, 25, 50, 100])
            ->defaultPaginationPageOption(25)
            ->groups([
                Group::make('status')
                    ->label('Tahap')
                    ->getTitleFromRecordUsing(fn ($record) => self::STAGES[$record->status] ?? $record->status),
            ])
            ->defaultGroup('status')
            ->columns([
                TextColumn::make('parentSaving.user.name')
                    ->label('Penabung')
                    ->searchable(),

                TextColumn::make('parentSaving.user.phone')
                    ->label('WhatsApp')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('parentSaving.package_code')
                    ->label('Paket')
                    ->badge()
                    ->formatStateUsing(fn ($state) => config("qurma.packages.$state.name", $state)),

                TextColumn::make('status')
                    ->label('Tahap')
                    ->badge()
                    ->formatStateUsing(fn ($state) => self::STAGES[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'executed'          => 'success',
                        'waiting_execution' => 'warning',
                        default             => 'info',
                    }),

                TextColumn::make('animal_weight')
                    ->label('Bobot')
                    ->suffix(' kg'),

                TextColumn::make('slaughter_date')
                    ->label('Tanggal')
                    ->date('d M Y'),

                TextColumn::make('location')
                    ->label('Lokasi')
                    ->toggleable(),

                TextColumn::make('vendor_name')
                    ->label('Vendor')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Tahap')
                    ->options(self::STAGES),

                SelectFilter::make('package')
                    ->label('Paket')
                    ->options(collect(config('qurma.packages'))->map(fn ($p) => $p['name'])->all())
                    ->query(fn (Builder $query, array $data) => filled($data['value'] ?? null)
                        ? $query->whereHas('parentSaving', fn ($q) => $q->where('package_code', $data['value']))
                        : $query),

                Filter::make('not_executed')
                    ->label('Belum dilaksanakan')
                    ->toggle()
                    ->query(fn (Builder $query) => $query->where('status', '!=', 'executed')),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('updateStage')
                        ->label('Ubah tahap terpilih')
                        ->icon('heroicon-o-arrow-path')
                        ->schema([
                            Select::make('status')
                                ->label('Tahap baru')
                                ->options(self::STAGES)
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each->update(['status' => $data['status']]);

                            Notification::make()
                                ->title($records->count() . ' persiapan qurban diperbarui')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('setExecution')
                        ->label('Atur jadwal, lokasi & vendor')
                        ->icon('heroicon-o-calendar-days')
                        ->schema([
                            DatePicker::make('slaughter_date')
                                ->label('Tanggal pelaksanaan')
                                ->native(false),
                            TextInput::make('location')
                                ->label('Lokasi pelaksanaan'),
                            TextInput::make('vendor_name')
                                ->label('Vendor / mitra'),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $filled = array_filter($data, fn ($value) => filled($value));

                            if ($filled === []) {
                                Notification::make()->title('Tidak ada isian yang diubah')->warning()->send();

                                return;
                            }

                            $records->each->update($filled);

                            Notification::make()
                                ->title($records->count() . ' persiapan qurban diperbarui')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}