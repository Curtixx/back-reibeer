<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    private const TOTAL = 50_000;

    private const CHUNK = 1_000;

    private array $adjectives = ['Original', 'Premium', 'Light', 'Extra', 'Gold', 'Ice', 'Dark', 'Pilsen', 'Stout', 'Lager'];

    private array $nouns = ['Cerveja', 'Chopp', 'Bebida', 'Drink', 'Suco', 'Água', 'Refrigerante', 'Energético', 'Vinho', 'IPA'];

    public function run(): void
    {
        $this->command->info('Seeding products...');
        $now = now();
        $batch = [];

        for ($i = 1; $i <= self::TOTAL; $i++) {
            $adj = $this->adjectives[($i - 1) % count($this->adjectives)];
            $noun = $this->nouns[(int) (($i - 1) / count($this->adjectives)) % count($this->nouns)];
            $costPrice = round(mt_rand(100, 5000) / 100, 2);
            $salePrice = round($costPrice * mt_rand(150, 250) / 100, 2);
            $hasPixPrice = $i % 3 !== 0;

            $batch[] = [
                'name' => "$adj $noun $i",
                'description' => $i % 5 === 0 ? "Descrição detalhada do produto $i" : null,
                'cost_price' => $costPrice,
                'sale_price' => $salePrice,
                'pix_price' => $hasPixPrice ? round($salePrice * 0.95, 2) : null,
                'stock_notice' => mt_rand(0, 20),
                'is_active' => $i % 10 !== 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($batch) === self::CHUNK) {
                DB::table('products')->insert($batch);
                $batch = [];
            }
        }

        if (! empty($batch)) {
            DB::table('products')->insert($batch);
        }

        $this->command->info('Products seeded: '.self::TOTAL);
    }
}
