<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityLogSeeder extends Seeder
{
    private const TOTAL = 50_000;

    private const CHUNK = 1_000;

    private array $modelTypes = [
        'App\\Models\\Product',
        'App\\Models\\Combo',
        'App\\Models\\Order',
        'App\\Models\\Sale',
        'App\\Models\\Cashier',
        'App\\Models\\Category',
    ];

    private array $actions = ['created', 'updated', 'deleted'];

    public function run(): void
    {
        $this->command->info('Seeding activity_logs...');

        $userId = DB::table('users')->value('id');
        $baseDate = now()->subYear();
        $batch = [];

        for ($i = 1; $i <= self::TOTAL; $i++) {
            $action = $this->actions[$i % count($this->actions)];
            $modelType = $this->modelTypes[$i % count($this->modelTypes)];
            $isUpdate = $action === 'updated';

            $batch[] = [
                'user_id' => $i % 20 === 0 ? null : $userId,
                'model_type' => $modelType,
                'model_id' => mt_rand(1, 50_000),
                'action' => $action,
                'column_name' => $isUpdate ? 'sale_price' : null,
                'old_value' => $isUpdate ? (string) round(mt_rand(500, 10000) / 100, 2) : null,
                'new_value' => $isUpdate ? (string) round(mt_rand(500, 10000) / 100, 2) : null,
                'description' => $i % 5 === 0 ? "Registro $i modificado via sistema" : null,
                'created_at' => $baseDate->copy()->addMinutes($i * 10),
            ];

            if (count($batch) === self::CHUNK) {
                DB::table('activity_logs')->insert($batch);
                $batch = [];
            }
        }

        if (! empty($batch)) {
            DB::table('activity_logs')->insert($batch);
        }

        $this->command->info('Activity logs seeded: '.self::TOTAL);
    }
}
