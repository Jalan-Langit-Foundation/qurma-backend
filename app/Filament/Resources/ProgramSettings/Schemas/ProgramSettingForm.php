<?php

namespace App\Filament\Resources\ProgramSettings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProgramSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('idul_adha_date')
                    ->label('Tanggal Idul Adha (resmi)')
                    ->native(false)
                    ->helperText('Kosongkan sampai tanggal resmi ditetapkan. Selama kosong, sistem memakai perkiraan dari konfigurasi.'),

                TextInput::make('payoff_lead_days')
                    ->label('Batas pelunasan (hari sebelum Idul Adha)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(60)
                    ->default(10)
                    ->required()
                    ->helperText('Sesuai arahan Kang Abay: H-10.'),

                DatePicker::make('registration_deadline')
                    ->label('Batas pendaftaran peserta baru')
                    ->native(false)
                    ->required()
                    ->helperText('Setelah tanggal ini pendaftaran lewat website ditutup. Admin tetap bisa membuat tabungan dari panel.'),

                Toggle::make('last_month_in_deadline')
                    ->label('Cicilan terakhir boleh jatuh di bulan batas pelunasan')
                    ->default(true)
                    ->helperText('Matikan bila jadwal cicilan sebaiknya berhenti sebulan sebelumnya (masih menunggu konfirmasi).'),
            ]);
    }
}