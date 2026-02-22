<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CashierSeeder extends Seeder
{
    private const TOTAL = 50_000;

    private const CHUNK = 1_000;

    public function run(): void
    {
        $this->command->info('Seeding cashiers...');

        $userId = DB::table('users')->value('id') ?? 1;
        $baseDate = now()->subYears(2);
        $batch = [];

        for ($i = 1; $i <= self::TOTAL; $i++) {
            $isClosed = $i % 5 !== 0;
            $openedAt = $baseDate->copy()->addMinutes($i * 30);
            $closedAt = $isClosed ? $openedAt->copy()->addHours(mt_rand(4, 12)) : null;

            $batch[] = [
                'initial_amount' => mt_rand(5000, 50000) / 100,
                'user_id_open' => $userId,
                'user_id_close' => $isClosed ? $userId : null,
                'opened_at' => $openedAt,
                'closed_at' => $closedAt,
                'total_sales' => $isClosed ? mt_rand(0, 500000) / 100 : 0,
                'status' => $isClosed ? 'closed' : 'open',
                'created_at' => $openedAt,
                'updated_at' => $closedAt ?? $openedAt,
            ];

            if (count($batch) === self::CHUNK) {
                DB::table('cashiers')->insert($batch);
                $batch = [];
            }
        }

        if (! empty($batch)) {
            DB::table('cashiers')->insert($batch);
        }

        $this->command->info('Cashiers seeded: '.self::TOTAL);
    }
}
