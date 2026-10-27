<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fulfillment extends Model
{
    protected $fillable = [
        'saving_id', 'status', 'vendor_name',
        'animal_weight', 'slaughter_date', 'location',
    ];

    protected $casts = [
        'slaughter_date' => 'date',
    ];


    public function parentSaving(): BelongsTo
    {
        return $this->belongsTo(Saving::class, 'saving_id');
    }
}