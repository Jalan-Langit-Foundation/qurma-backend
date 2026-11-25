<?php

namespace App\Filament\Resources\ProgramSettings;

use App\Filament\Resources\ProgramSettings\Pages\CreateProgramSetting;
use App\Filament\Resources\ProgramSettings\Pages\EditProgramSetting;
use App\Filament\Resources\ProgramSettings\Pages\ListProgramSettings;
use App\Filament\Resources\ProgramSettings\Schemas\ProgramSettingForm;
use App\Filament\Resources\ProgramSettings\Tables\ProgramSettingsTable;
use App\Models\ProgramSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ProgramSettingResource extends Resource
{
    protected static ?string $model = ProgramSetting::class;

    protected static ?string $modelLabel = 'Pengaturan Program';

    protected static ?string $pluralModelLabel = 'Pengaturan Program';

    protected static ?string $navigationLabel = 'Pengaturan Program';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    // Pengaturan hanya satu baris: tidak bisa ditambah atau dihapus
    public static function canCreate(): bool
    {
        return ! ProgramSetting::query()->exists();
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return ProgramSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProgramSettingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListProgramSettings::route('/'),
            'create' => CreateProgramSetting::route('/create'),
            'edit'   => EditProgramSetting::route('/{record}/edit'),
        ];
    }
}