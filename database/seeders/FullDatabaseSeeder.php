<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FullDatabaseSeeder extends Seeder
{
    private const CHUNK = 1_000;

    public function run(): void
    {
        $this->command->info('=== Iniciando seed completo do banco ===');
        $startTime = microtime(true);

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $this->ensureUserExists();

        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            ComboSeeder::class,
            StockSeeder::class,
            CashierSeeder::class,
            SaleSeeder::class,
            OrderSeeder::class,
            ActivityLogSeeder::class,
        ]);

        $this->seedCategoryProducts();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $elapsed = round(microtime(true) - $startTime, 2);
        $this->command->info("=== Seed completo finalizado em {$elapsed}s ===");
    }

    private function ensureUserExists(): void
    {
        if (! DB::table('users')->exists()) {
            $this->command->info('Criando usuário padrão...');
            $this->call(DatabaseSeeder::class);
        } else {
            $this->command->info('Usuário já existe, pulando.');
        }
    }

    private function seedCategoryProducts(): void
    {
        $this->command->info('Seeding category_product pivot...');
        $now = now();

        /** @var int $minCategoryId */
        $minCategoryId = DB::table('categories')->min('id') ?? 1;
        /** @var int $maxCategoryId */
        $maxCategoryId = DB::table('categories')->max('id') ?? 50_000;
        $categoryRange = $maxCategoryId - $minCategoryId;

        /** @var int $minProductId */
        $minProductId = DB::table('products')->min('id') ?? 1;
        /** @var int $maxProductId */
        $maxProductId = DB::table('products')->max('id') ?? 50_000;
        $productRange = $maxProductId - $minProductId;

        $batch = [];
        $count = 0;

        // Assign 1 category per product (deterministic, no duplicates possible)
        for ($productId = $minProductId; $productId <= $maxProductId; $productId++) {
            $categoryId = $minCategoryId + ($productId - 1) % ($categoryRange + 1);

            $batch[] = [
                'category_id' => $categoryId,
                'product_id' => $productId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $count++;

            if (count($batch) === self::CHUNK) {
                DB::table('category_product')->insert($batch);
                $batch = [];
            }
        }

        if (! empty($batch)) {
            DB::table('category_product')->insert($batch);
        }

        $this->command->info("Category products seeded: $count");
    }
}
