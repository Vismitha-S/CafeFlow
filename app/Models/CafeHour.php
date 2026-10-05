<?php

namespace App\Models;

use Database\Factories\CafeHourFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CafeHour extends Model
{
    /** @use HasFactory<CafeHourFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'is_closed' => 'boolean',
    ];

    public function cafe(): BelongsTo
    {
        return $this->belongsTo(Cafe::class);
    }
}
