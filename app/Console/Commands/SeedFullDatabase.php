<?php

namespace App\Console\Commands;

use Database\Seeders\FullDatabaseSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SeedFullDatabase extends Command
{
    protected $signature = 'db:seed-full
                            {--fresh : Executa migrate:fresh antes de popular}
                            {--force : Pula a confirmação (necessário em produção)}';

    protected $description = 'Popula todas as tabelas com ~50k registros cada (exceto users e employees)';

    public function handle(): int
    {
        if (! $this->option('force') && ! $this->confirm('Isso irá popular o banco com ~50k registros por tabela. Continuar?', true)) {
            $this->info('Operação cancelada.');

            return self::SUCCESS;
        }

        if ($this->option('fresh')) {
            $this->info('Rodando migrate:fresh...');
            Artisan::call('migrate:fresh', ['--force' => true], $this->output);
        }

        $this->call('db:seed', [
            '--class' => FullDatabaseSeeder::class,
            '--force' => true,
        ]);

        return self::SUCCESS;
    }
}
