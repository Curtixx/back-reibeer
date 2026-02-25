<?php

namespace App\Console\Commands;

use App\Models\Permission;
use Illuminate\Console\Command;

class Permissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:permissions {prefix=api}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make permissions for routes';

    protected array $routesNotList = [
        'login',
        'register',
        'logout',
        'refresh',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $prefixToSearch = $this->argument('prefix');
        $routes = \Illuminate\Support\Facades\Route::getRoutes();
        $groups = [];

        foreach ($routes as $route) {
            $uri = $route->uri();
            
            if (str_starts_with($uri, $prefixToSearch . '/') && $uri !== $prefixToSearch) {
                $uriWithoutBase = substr($uri, strlen($prefixToSearch) + 1);
                
                $group = explode('/', $uriWithoutBase)[0];
                
                if (!empty($group) && !in_array($group, $this->routesNotList)) {
                    $groups[$group] = true;
                }
            }
        }

        $groups = array_keys($groups);
        sort($groups);

        if (empty($groups)) {
            $this->warn("Nenhum grupo encontrado com o prefixo '{$prefixToSearch}'");
            return;
        }

        $this->info("=== Criando permissões '{$prefixToSearch}' ===");
        foreach ($groups as $group) {
            Permission::updateOrCreate([
                'name' => $group,
            ]);
        }
    }
}
