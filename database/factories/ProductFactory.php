<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->optional()->sentence(),
            'cost_price' => fake()->randomFloat(2, 1, 50),
            'sale_price' => fake()->randomFloat(2, 5, 100),
            'pix_price' => fake()->optional(0.7)->randomFloat(2, 4, 95),
            'stock_notice' => fake()->numberBetween(0, 20),
            'is_active' => fake()->boolean(90),
        ];
    }
}
