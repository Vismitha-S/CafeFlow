<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reservation extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'reservation_date' => 'date',
            'reservation_fee' => 'decimal:2',
            'cancellation_penalty_percentage' => 'decimal:2',
            'cancellation_penalty_amount' => 'decimal:2',
            'cancelled_at' => 'datetime',
        ];
    }

    // Accessor and mutator for start_time in H:i format
    protected function startTime(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? Carbon::parse($value)->format('H:i') : null,
            set: fn (?string $value) => $value ? Carbon::parse($value)->format('H:i:s') : null,
        );
    }

    // Accessor and mutator for end_time in H:i format
    protected function endTime(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? Carbon::parse($value)->format('H:i') : null,
            set: fn (?string $value) => $value ? Carbon::parse($value)->format('H:i:s') : null,
        );
    }

    // Belongs to User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Belongs to Cafe
    public function cafe(): BelongsTo
    {
        return $this->belongsTo(Cafe::class);
    }

    // Belongs to CafeTable
    public function cafeTable(): BelongsTo
    {
        return $this->belongsTo(CafeTable::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // Scope for blocking reservations
    public function scopeBlocking($query)
    {
        return $query->whereIn('status', config('reservations.blocking_statuses', ['pending', 'confirmed']));
    }
}
