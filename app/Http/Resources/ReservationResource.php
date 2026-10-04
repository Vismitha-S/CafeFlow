<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    // Disable automatic 'data' wrapping
    public static $wrap = null;

    // Transform the resource into an array
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cafe' => $this->whenLoaded('cafe', function () {
                return [
                    'id' => $this->cafe->id,
                    'name' => $this->cafe->name,
                    'slug' => $this->cafe->slug,
                    'address' => $this->cafe->address,
                    'city' => $this->cafe->city,
                ];
            }, [
                'id' => $this->cafe_id,
            ]),
            'table' => $this->whenLoaded('cafeTable', function () {
                return [
                    'id' => $this->cafeTable->id,
                    'table_number' => $this->cafeTable->table_number,
                    'name' => $this->cafeTable->name,
                    'capacity' => $this->cafeTable->capacity,
                    'location' => $this->cafeTable->location,
                ];
            }, [
                'id' => $this->cafe_table_id,
            ]),
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            'reservation_date' => $this->reservation_date instanceof \DateTimeInterface
                ? $this->reservation_date->format('Y-m-d')
                : (string) $this->reservation_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'guest_count' => (int) $this->guest_count,
            'status' => $this->status,
            'reservation_fee' => (string) $this->reservation_fee,
            'cancellation_penalty_percentage' => (string) $this->cancellation_penalty_percentage,
            'cancellation_penalty_amount' => $this->cancellation_penalty_amount ? (string) $this->cancellation_penalty_amount : null,
            'cancelled_at' => $this->cancelled_at?->toIso8601String(),
            'cancellation_reason' => $this->cancellation_reason,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
