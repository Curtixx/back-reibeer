<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use NumberFormatter;

class DashboardService
{
    private NumberFormatter $formatValue;

    public function __construct()
    {
        $this->formatValue = new NumberFormatter('pt_BR', NumberFormatter::CURRENCY);
    }

    /**
     * Get total profit for current and previous month with percentage change
     */
    public function getProfitData(): array
    {
        $currentMonth = Carbon::now();
        $previousMonth = Carbon::now()->subMonth();

        $currentProfit = $this->calculateProfit($currentMonth);
        $previousProfit = $this->calculateProfit($previousMonth);

        $percentage = $this->calculatePercentageChange($previousProfit, $currentProfit);

        return [
            'current_month' => $this->formatValue->format($currentProfit),
            'previous_month' => $this->formatValue->format($previousProfit),
            'percentage_change' => $percentage,
        ];
    }

    /**
     * Get total expenses for current and previous month with percentage change
     */
    public function getExpensesData(): array
    {
        $currentMonth = Carbon::now();
        $previousMonth = Carbon::now()->subMonth();

        $currentExpenses = $this->calculateExpenses($currentMonth);
        $previousExpenses = $this->calculateExpenses($previousMonth);

        $percentage = $this->calculatePercentageChange($previousExpenses, $currentExpenses);

        return [
            'current_month' => $this->formatValue->format($currentExpenses),
            'previous_month' => $this->formatValue->format($previousExpenses),
            'percentage_change' => $percentage,
        ];
    }

    /**
     * Get average ticket for current and previous month with percentage change
     */
    public function getAverageTicketData(): array
    {
        $currentMonth = Carbon::now();
        $previousMonth = Carbon::now()->subMonth();

        $currentAverage = $this->calculateAverageTicket($currentMonth);
        $previousAverage = $this->calculateAverageTicket($previousMonth);

        $percentage = $this->calculatePercentageChange($previousAverage, $currentAverage);

        return [
            'current_month' => $this->formatValue->format($currentAverage),
            'previous_month' => $this->formatValue->format($previousAverage),
            'percentage_change' => $percentage,
        ];
    }

    /**
     * Get total orders for current and previous month with percentage change
     */
    public function getTotalOrdersData(): array
    {
        $currentMonth = Carbon::now();
        $previousMonth = Carbon::now()->subMonth();

        $currentTotal = Order::whereYear('created_at', $currentMonth->year)
            ->whereMonth('created_at', $currentMonth->month)
            ->count();

        $previousTotal = Order::whereYear('created_at', $previousMonth->year)
            ->whereMonth('created_at', $previousMonth->month)
            ->count();

        $percentage = $this->calculatePercentageChange($previousTotal, $currentTotal);

        return [
            'current_month' => $this->formatValue->format($currentTotal),
            'previous_month' => $this->formatValue->format($previousTotal),
            'percentage_change' => $percentage,
        ];
    }

    /**
     * Get paginated sales data by period (week, month, year)
     */
    public function getSalesByPeriod(string $period = 'month'): array
    {
        $query = Sale::query();

        switch ($period) {
            case 'week':
                $query->whereBetween('created_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek(),
                ]);
                break;
            case 'month':
                $query->whereYear('created_at', Carbon::now()->year)
                    ->whereMonth('created_at', Carbon::now()->month);
                break;
            case 'year':
                $query->whereYear('created_at', Carbon::now()->year);
                break;
        }

        return [
            'count' => $query->count(),
            'total_amount' => $this->formatValue->format($query->sum('total_amount')),
        ];
    }

    /**
     * Get top 5 most sold products
     */
    public function getTopSellingProducts(): array
    {
        return SaleItem::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->with('product')
            ->groupBy('product_id')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'product_name' => $item->product->name,
                    'total_sold' => $item->total_quantity,
                ];
            })
            ->toArray();
    }

    /**
     * Get products with low stock (less than 10 units)
     */
    public function getLowStockProducts(): array
    {
        return Stock::where('quantity', '<', 10)
            ->with('product')
            ->get()
            ->map(function ($stock) {
                return [
                    'product_id' => $stock->product_id,
                    'product_name' => $stock->product->name,
                    'quantity' => $stock->quantity,
                ];
            })
            ->toArray();
    }

    /**
     * Get profit for all months of current year
     */
    public function getProfitByMonth(): array
    {
        $currentYear = Carbon::now()->year;
        $profits = [];

        for ($month = 1; $month <= 12; $month++) {
            $date = Carbon::create($currentYear, $month, 1);
            $profit = $this->calculateProfit($date);

            $profits[] = [
                'month' => $month,
                'month_name' => $date->format('F'),
                'profit' => $this->formatValue->format($profit),
            ];
        }

        return $profits;
    }

    /**
     * Calculate profit for a specific month
     */
    private function calculateProfit(Carbon $date): float
    {
        $sales = Sale::whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->sum('total_amount');

        $expenses = $this->calculateExpenses($date);

        return $sales - $expenses;
    }

    /**
     * Calculate expenses (cost) for a specific month
     */
    private function calculateExpenses(Carbon $date): float
    {
        return SaleItem::query()
            ->whereHas('sale', function ($query) use ($date) {
                $query->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month);
            })
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->sum(DB::raw('sale_items.quantity * products.cost_price'));
    }

    /**
     * Calculate average ticket for a specific month
     */
    private function calculateAverageTicket(Carbon $date): float
    {
        $totalSales = Sale::whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->sum('total_amount');

        $salesCount = Sale::whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->count();

        return $salesCount > 0 ? $totalSales / $salesCount : 0;
    }

    /**
     * Calculate percentage change between two values
     */
    private function calculatePercentageChange(float $oldValue, float $newValue): float
    {
        if ($oldValue == 0) {
            return $newValue > 0 ? 100 : 0;
        }

        return (($newValue - $oldValue) / $oldValue) * 100;
    }
}
