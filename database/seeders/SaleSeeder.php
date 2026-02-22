<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SaleSeeder extends Seeder
{
    private const TOTAL_SALES = 50_000;

    private const ITEMS_PER_SALE = 2;

    private const CHUNK = 1_000;

    private array $paymentMethods = ['DINHEIRO', 'PIX', 'CARTAO_CREDITO', 'CARTAO_DEBITO'];

    public function run(): void
    {
        $this->command->info('Seeding sales...');
        $now = now();

        /** @var int $minCashierId */
        $minCashierId = DB::table('cashiers')->min('id') ?? 1;
        /** @var int $maxCashierId */
        $maxCashierId = DB::table('cashiers')->max('id') ?? 1;
        $cashierRange = $maxCashierId - $minCashierId;

        /** @var int $minProductId */
        $minProductId = DB::table('products')->min('id') ?? 1;
        /** @var int $maxProductId */
        $maxProductId = DB::table('products')->max('id') ?? 50_000;
        $productRange = $maxProductId - $minProductId;

        $saleBatch = [];
        $saleInsertedIds = [];

        for ($i = 1; $i <= self::TOTAL_SALES; $i++) {
            $cashierId = $minCashierId + ($i % ($cashierRange + 1));
            $totalAmount = round(mt_rand(500, 50000) / 100, 2);
            $paymentMethod = $this->paymentMethods[$i % count($this->paymentMethods)];

            $saleBatch[] = [
                'payment_method' => $paymentMethod,
                'total_amount' => $totalAmount,
                'cashier_id' => $cashierId,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($saleBatch) === self::CHUNK) {
                DB::table('sales')->insert($saleBatch);
                $saleBatch = [];
            }
        }

        if (! empty($saleBatch)) {
            DB::table('sales')->insert($saleBatch);
        }

        $this->command->info('Sales seeded: '.self::TOTAL_SALES);
        $this->command->info('Seeding sale_items...');

        /** @var int $minSaleId */
        $minSaleId = DB::table('sales')->min('id') ?? 1;
        /** @var int $maxSaleId */
        $maxSaleId = DB::table('sales')->max('id') ?? self::TOTAL_SALES;

        $itemBatch = [];
        $itemCount = 0;

        for ($saleId = $minSaleId; $saleId <= $maxSaleId; $saleId++) {
            $numItems = self::ITEMS_PER_SALE;
            $usedProducts = [];

            for ($p = 0; $p < $numItems; $p++) {
                $productId = $minProductId + ($saleId + $p * 6151) % ($productRange + 1);

                $unitPrice = round(mt_rand(300, 10000) / 100, 2);
                $discount = $p === 0 ? 0 : round(mt_rand(0, 200) / 100, 2);

                $itemBatch[] = [
                    'sale_id' => $saleId,
                    'product_id' => $productId,
                    'quantity' => mt_rand(1, 5),
                    'unit_price' => $unitPrice,
                    'discount' => $discount,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $itemCount++;
            }

            if (count($itemBatch) >= self::CHUNK) {
                DB::table('sale_items')->insert($itemBatch);
                $itemBatch = [];
            }
        }

        if (! empty($itemBatch)) {
            DB::table('sale_items')->insert($itemBatch);
        }

        $this->command->info("Sale items seeded: $itemCount");
    }
}
