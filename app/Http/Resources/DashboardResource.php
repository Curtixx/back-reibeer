<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'profit' => [
                'current_month' => $this->resource['profit']['current_month'],
                'previous_month' => $this->resource['profit']['previous_month'],
                'percentage_change' => round($this->resource['profit']['percentage_change'], 2),
            ],
            'expenses' => [
                'current_month' => $this->resource['expenses']['current_month'],
                'previous_month' => $this->resource['expenses']['previous_month'],
                'percentage_change' => round($this->resource['expenses']['percentage_change'], 2),
            ],
            'average_ticket' => [
                'current_month' => $this->resource['average_ticket']['current_month'],
                'previous_month' => $this->resource['average_ticket']['previous_month'],
                'percentage_change' => round($this->resource['average_ticket']['percentage_change'], 2),
            ],
            'total_orders' => [
                'current_month' => $this->resource['total_orders']['current_month'],
                'previous_month' => $this->resource['total_orders']['previous_month'],
                'percentage_change' => round($this->resource['total_orders']['percentage_change'], 2),
            ],
            'sales_by_period' => $this->resource['sales_by_period'],
            'top_selling_products' => $this->resource['top_selling_products'],
            'low_stock_products' => $this->resource['low_stock_products'],
            'profit_by_month' => $this->resource['profit_by_month'],
        ];
    }
}
