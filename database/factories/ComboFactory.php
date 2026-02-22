<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Combo>
 */
class ComboFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Combo '.fake()->words(2, true),
            'sale_price' => fake()->randomFloat(2, 10, 150),
            'pix_price' => fake()->optional(0.7)->randomFloat(2, 9, 140),
            'is_active' => fake()->boolean(90),
        ];
    }
}
