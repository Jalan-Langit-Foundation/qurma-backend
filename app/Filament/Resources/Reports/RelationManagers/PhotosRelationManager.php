<?php

namespace App\Filament\Resources\Reports\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PhotosRelationManager extends RelationManager
{
    protected static string $relationship = 'photos';

    protected static ?string $title = 'Foto dokumentasi';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->label('Kategori')
                    ->options([
                        'animal'       => 'Foto hewan',
                        'slaughter'    => 'Proses penyembelihan',
                        'distribution' => 'Distribusi',
                    ])
                    ->required(),

                FileUpload::make('file_path')
                    ->label('Foto')
                    ->image()
                    ->disk('public')
                    ->directory('reports')
                    ->visibility('public')
                    ->maxSize(4096)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type')
            ->columns([
                ImageColumn::make('file_path')
                    ->label('Foto')
                    ->disk('public'),

                TextColumn::make('type')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'animal'       => 'Foto hewan',
                        'slaughter'    => 'Penyembelihan',
                        'distribution' => 'Distribusi',
                        default        => $state,
                    }),
            ])
            ->headerActions([
                CreateAction::make()->label('Tambah foto'),
            ])
            ->recordActions([
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}