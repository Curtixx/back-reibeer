<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardResource;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function __construct(public DashboardService $dashboardService) {}

    /**
     * Get dashboard data
     */
    public function index(Request $request)
    {
        try {
            $period = $request->query('period', 'month');
            $perPage = $request->query('per_page', 15);
            $cacheKey = 'dashboard_data:period:' . $period . ':per_page:' . $perPage;

            $dashboardData = Cache::tags(['dashboard'])->remember($cacheKey, 3600, function () use ($period, $perPage) {
                return [
                    'profit' => $this->dashboardService->getProfitData(),
                    'expenses' => $this->dashboardService->getExpensesData(),
                    'average_ticket' => $this->dashboardService->getAverageTicketData(),
                    'total_orders' => $this->dashboardService->getTotalOrdersData(),
                    'sales_by_period' => $this->dashboardService->getSalesByPeriod($period),
                    'top_selling_products' => $this->dashboardService->getTopSellingProducts(),
                    'low_stock_products' => $this->dashboardService->getLowStockProducts(),
                    'profit_by_month' => $this->dashboardService->getProfitByMonth(),
                ];
            });

            return response()->json(new DashboardResource($dashboardData), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao buscar dados do dashboard!'], 500);
        }
    }
}
