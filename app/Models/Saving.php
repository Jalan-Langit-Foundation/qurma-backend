<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Saving extends Model
{
    protected $fillable = [
        'user_id', 'package_code', 'target_amount',
        'tenor_months', 'start_month', 'program_year', 'status',
    ];

    protected $casts = [
        'start_month' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }

    public function fulfillment(): HasOne
    {
        return $this->hasOne(Fulfillment::class);
    }

    public function report(): HasOne
    {
        return $this->hasOne(Report::class);
    }

    public function refreshStatus(): void
{
    if ($this->status === 'cancelled') {
        return;
    }

    $paid   = (int) $this->deposits()->sum('amount');
    $status = $paid >= $this->target_amount ? 'completed' : 'active';

    if ($status !== $this->status) {
        $this->update(['status' => $status]);
    }
}
}