<?php

namespace Database\Factories;

use App\Models\CafeTable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CafeTable>
 */
class CafeTableFactory extends Factory
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
            'table_number' => $this->faker->unique()->numerify('T-####'),
            'name' => $this->faker->word() . ' Table',
            'capacity' => $this->faker->numberBetween(1, 10),
            'location' => 'indoor',
            'status' => 'active',
        ];
    }
}
