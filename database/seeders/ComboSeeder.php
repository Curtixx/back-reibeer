<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComboSeeder extends Seeder
{
    private const TOTAL = 50_000;

    private const CHUNK = 1_000;

    private const PRODUCTS_PER_COMBO = 2;

    public function run(): void
    {
        $this->command->info('Seeding combos...');
        $now = now();

        /** @var int $minProductId */
        $minProductId = DB::table('products')->min('id') ?? 1;
        /** @var int $maxProductId */
        $maxProductId = DB::table('products')->max('id') ?? 50_000;
        $productRange = $maxProductId - $minProductId;

        $comboBatch = [];
        $pivotBatch = [];
        $comboIds = [];

        for ($i = 1; $i <= self::TOTAL; $i++) {
            $salePrice = round(mt_rand(1000, 15000) / 100, 2);
            $hasPixPrice = $i % 3 !== 0;

            $comboBatch[] = [
                'name' => "Combo $i",
                'sale_price' => $salePrice,
                'pix_price' => $hasPixPrice ? round($salePrice * 0.93, 2) : null,
                'is_active' => $i % 10 !== 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($comboBatch) === self::CHUNK) {
                DB::table('combos')->insert($comboBatch);
                $comboBatch = [];
            }
        }

        if (! empty($comboBatch)) {
            DB::table('combos')->insert($comboBatch);
        }

        $this->command->info('Combos seeded: '.self::TOTAL);
        $this->command->info('Seeding combo_products...');

        /** @var int $minComboId */
        $minComboId = DB::table('combos')->min('id') ?? 1;
        /** @var int $maxComboId */
        $maxComboId = DB::table('combos')->max('id') ?? self::TOTAL;

        for ($comboId = $minComboId; $comboId <= $maxComboId; $comboId++) {
            $usedProducts = [];
            for ($p = 0; $p < self::PRODUCTS_PER_COMBO; $p++) {
                do {
                    $productId = $minProductId + ($comboId + $p * 7919) % ($productRange + 1);
                } while (in_array($productId, $usedProducts, true));

                $usedProducts[] = $productId;

                $pivotBatch[] = [
                    'combo_id' => $comboId,
                    'product_id' => $productId,
                    'quantity' => mt_rand(1, 4),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if (count($pivotBatch) >= self::CHUNK) {
                DB::table('combo_products')->insert($pivotBatch);
                $pivotBatch = [];
            }
        }

        if (! empty($pivotBatch)) {
            DB::table('combo_products')->insert($pivotBatch);
        }

        $this->command->info('Combo products seeded: '.(self::TOTAL * self::PRODUCTS_PER_COMBO));
    }
}
