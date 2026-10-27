<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    protected $fillable = ['saving_id', 'notes', 'published_at'];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function parentSaving(): BelongsTo
{
    return $this->belongsTo(Saving::class, 'saving_id');
}

    public function photos(): HasMany
    {
        return $this->hasMany(ReportPhoto::class);
    }
}