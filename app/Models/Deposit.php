<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deposit extends Model
{
    protected $fillable = ['saving_id', 'recorded_by', 'amount', 'paid_at', 'note'];

    protected $casts = [
        'paid_at' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Deposit $deposit) {
            $deposit->recorded_by ??= auth()->id();
        });

        // Diubah menggunakan parentSaving sesuai dengan nama relasi di bawah
        static::saved(fn (Deposit $deposit) => $deposit->parentSaving?->refreshStatus());
        static::deleted(fn (Deposit $deposit) => $deposit->parentSaving?->refreshStatus());
    }

    public function parentSaving(): BelongsTo
    {
        return $this->belongsTo(Saving::class, 'saving_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}