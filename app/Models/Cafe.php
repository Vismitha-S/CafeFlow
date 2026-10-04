<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cafe extends Model
{
    /** @use HasFactory<\Database\Factories\CafeFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'reservation_fee' => 'decimal:2',
        'cancellation_penalty_percentage' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function hours(): HasMany
    {
        return $this->hasMany(CafeHour::class);
    }

    public function tables(): HasMany
    {
        return $this->hasMany(CafeTable::class);
    }
}
