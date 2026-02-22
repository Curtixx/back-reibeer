<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'number' => 'ORD-'.fake()->unique()->numerify('######'),
            'responsible_name' => fake()->name(),
            'status' => fake()->randomElement(['open', 'closed']),
            'is_active' => fake()->boolean(90),
        ];
    }
}
