<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    private const TOTAL_ORDERS = 50_000;

    private const ITEMS_PER_ORDER = 2;

    private const CHUNK = 1_000;

    private array $names = ['Ana Silva', 'Bruno Costa', 'Carlos Souza', 'Diego Lima', 'Eduardo Santos',
        'Fernanda Oliveira', 'Gabriel Pereira', 'Helena Martins', 'Igor Ferreira', 'Julia Alves'];

    public function run(): void
    {
        $this->command->info('Seeding orders...');
        $now = now();

        /** @var int $minProductId */
        $minProductId = DB::table('products')->min('id') ?? 1;
        /** @var int $maxProductId */
        $maxProductId = DB::table('products')->max('id') ?? 50_000;
        $productRange = $maxProductId - $minProductId;

        $orderBatch = [];

        for ($i = 1; $i <= self::TOTAL_ORDERS; $i++) {
            $orderBatch[] = [
                'number' => sprintf('ORD-%08d', $i),
                'responsible_name' => $this->names[$i % count($this->names)],
                'status' => $i % 4 === 0 ? 'closed' : 'open',
                'is_active' => $i % 10 !== 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($orderBatch) === self::CHUNK) {
                DB::table('orders')->insert($orderBatch);
                $orderBatch = [];
            }
        }

        if (! empty($orderBatch)) {
            DB::table('orders')->insert($orderBatch);
        }

        $this->command->info('Orders seeded: '.self::TOTAL_ORDERS);
        $this->command->info('Seeding order_products...');

        /** @var int $minOrderId */
        $minOrderId = DB::table('orders')->min('id') ?? 1;
        /** @var int $maxOrderId */
        $maxOrderId = DB::table('orders')->max('id') ?? self::TOTAL_ORDERS;

        $pivotBatch = [];
        $pivotCount = 0;

        for ($orderId = $minOrderId; $orderId <= $maxOrderId; $orderId++) {
            $usedProducts = [];
            for ($p = 0; $p < self::ITEMS_PER_ORDER; $p++) {
                $productId = $minProductId + ($orderId + $p * 4799) % ($productRange + 1);

                $pivotBatch[] = [
                    'order_id' => $orderId,
                    'product_id' => $productId,
                    'quantity' => mt_rand(1, 10),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $pivotCount++;
            }

            if (count($pivotBatch) >= self::CHUNK) {
                DB::table('order_products')->insert($pivotBatch);
                $pivotBatch = [];
            }
        }

        if (! empty($pivotBatch)) {
            DB::table('order_products')->insert($pivotBatch);
        }

        $this->command->info("Order products seeded: $pivotCount");
    }
}
