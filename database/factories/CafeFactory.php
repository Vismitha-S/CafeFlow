<?php

namespace Database\Factories;

use App\Models\Cafe;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cafe>
 */
class CafeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner_id' => \App\Models\User::factory(),
            'name' => $this->faker->company(),
            'slug' => $this->faker->unique()->slug(),
            'description' => $this->faker->paragraph(),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'reservation_fee' => $this->faker->randomFloat(2, 0, 100),
            'cancellation_penalty_percentage' => $this->faker->randomFloat(2, 0, 100),
            'status' => 'inactive',
        ];
    }
}
