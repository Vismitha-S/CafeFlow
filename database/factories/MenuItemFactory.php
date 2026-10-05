<?php

namespace Database\Factories;

use App\Models\Cafe;
use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MenuItem>
 */
class MenuItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cafe_id' => Cafe::factory(),
            'menu_category_id' => null,
            'name' => $this->faker->word().' Item',
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 5, 5000),
            'is_available' => true,
            'sort_order' => $this->faker->numberBetween(0, 100),
            'status' => 'active',
        ];
    }
}
