<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cashier>
 */
class CashierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isClosed = fake()->boolean(80);
        $openedAt = fake()->dateTimeBetween('-2 years', 'now');

        return [
            'initial_amount' => fake()->randomFloat(2, 50, 500),
            'user_id_open' => 1,
            'user_id_close' => $isClosed ? 1 : null,
            'opened_at' => $openedAt,
            'closed_at' => $isClosed ? fake()->dateTimeBetween($openedAt, 'now') : null,
            'total_sales' => fake()->randomFloat(2, 0, 5000),
            'status' => $isClosed ? 'closed' : 'open',
        ];
    }
}
