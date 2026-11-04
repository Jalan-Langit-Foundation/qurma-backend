<?php

namespace App\Filament\Resources\Reports\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('parentSaving.user')->withCount('photos'))
            ->columns([
                TextColumn::make('parentSaving.user.name')
                    ->label('Penabung')
                    ->searchable(),

                TextColumn::make('photos_count')
                    ->label('Foto'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($record): string => $record->isPublished() ? 'success' : ($record->published_at ? 'warning' : 'gray'))
                    ->formatStateUsing(fn ($record): string => $record->isPublished() ? 'Dipublikasikan' : ($record->published_at ? 'Terjadwal' : 'Draf')),
                    
                TextColumn::make('published_at')
                    ->label('Tanggal publish')
                    ->dateTime('d M Y H:i'),
            ])
            ->recordActions([
                Action::make('publish')
                    ->label('Publish')
                    ->icon('heroicon-o-paper-airplane')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->published_at === null)
                    ->action(fn ($record) => $record->update(['published_at' => now()])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}