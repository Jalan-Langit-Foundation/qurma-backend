<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramSetting extends Model
{
    protected $fillable = [
        'idul_adha_date', 'payoff_lead_days', 'registration_deadline', 'last_month_in_deadline',
    ];

    protected $casts = [
        'idul_adha_date'         => 'date',
        'registration_deadline'  => 'date',
        'last_month_in_deadline' => 'boolean',
    ];

    /** Pengaturan program aktif (satu baris). Dibuat otomatis kalau belum ada. */
    public static function current(): self
    {
        return static::query()->first() ?? static::query()->create([
            'idul_adha_date'         => null,
            'payoff_lead_days'       => 10,
            'registration_deadline'  => '2027-04-30',
            'last_month_in_deadline' => true,
        ]);
    }
}