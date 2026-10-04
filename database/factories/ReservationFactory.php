<?php

namespace Database\Factories;

use App\Models\Cafe;
use App\Models\CafeTable;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    // Define the model's default state.
    public function definition(): array
    {
        $duration = config('reservations.default_duration_minutes', 90);
        $start = Carbon::parse('12:00:00');
        $end = (clone $start)->addMinutes($duration);

        return [
            'cafe_id' => Cafe::factory(),
            'cafe_table_id' => function (array $attributes) {
                return CafeTable::factory()->create(['cafe_id' => $attributes['cafe_id']])->id;
            },
            'user_id' => User::factory(),
            'reservation_date' => Carbon::tomorrow()->toDateString(),
            'start_time' => $start->format('H:i'),
            'end_time' => $end->format('H:i'),
            'guest_count' => 2,
            'status' => 'confirmed',
            'reservation_fee' => 10.00,
            'cancellation_penalty_percentage' => 50.00,
            'cancellation_penalty_amount' => null,
            'cancelled_at' => null,
            'cancellation_reason' => null,
            'notes' => null,
        ];
    }

    // State for pending status
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    // State for confirmed status
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
        ]);
    }

    // State for cancelled status
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => 'Customer requested cancellation',
            'cancellation_penalty_amount' => 5.00,
        ]);
    }

    // State for completed status
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    // State for no_show status
    public function noShow(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'no_show',
        ]);
    }
}
