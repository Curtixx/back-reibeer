<?php

namespace App\Console\Commands;

use App\Services\ProductService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class StockLowCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:stock-low';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send low stock notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $productService = app(ProductService::class);
        $productService->sendLowStockNotifications();
    }
}
