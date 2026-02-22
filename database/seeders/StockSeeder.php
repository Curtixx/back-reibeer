<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockSeeder extends Seeder
{
    private const CHUNK = 1_000;

    public function run(): void
    {
        $this->command->info('Seeding stocks (one per product)...');
        $now = now();

        /** @var int $minId */
        $minId = DB::table('products')->min('id') ?? 1;
        /** @var int $maxId */
        $maxId = DB::table('products')->max('id') ?? 1;

        $batch = [];
        $count = 0;

        for ($productId = $minId; $productId <= $maxId; $productId++) {
            $batch[] = [
                'product_id' => $productId,
                'quantity' => mt_rand(0, 500),
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $count++;

            if (count($batch) === self::CHUNK) {
                DB::table('stocks')->insert($batch);
                $batch = [];
            }
        }

        if (! empty($batch)) {
            DB::table('stocks')->insert($batch);
        }

        $this->command->info("Stocks seeded: $count");
    }
}
