<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    private const TOTAL = 50_000;

    private const CHUNK = 1_000;

    public function run(): void
    {
        $this->command->info('Seeding categories...');
        $now = now();
        $batch = [];

        for ($i = 1; $i <= self::TOTAL; $i++) {
            $batch[] = [
                'name' => "Categoria $i",
                'is_active' => $i % 10 !== 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($batch) === self::CHUNK) {
                DB::table('categories')->insert($batch);
                $batch = [];
            }
        }

        if (! empty($batch)) {
            DB::table('categories')->insert($batch);
        }

        $this->command->info('Categories seeded: '.self::TOTAL);
    }
}
