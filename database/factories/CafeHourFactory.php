<?php

namespace Database\Factories;

use App\Models\CafeHour;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CafeHour>
 */
class CafeHourFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cafe_id' => \App\Models\Cafe::factory(),
            'day_of_week' => $this->faker->numberBetween(1, 7),
            'opens_at' => '08:00:00',
            'closes_at' => '17:00:00',
            'is_closed' => false,
        ];
    }
}
