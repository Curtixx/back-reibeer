<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'is_low_stock' => $this->is_low_stock,
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'sale_price' => $this->product->sale_price,
                'cost_price' => $this->product->cost_price,
                'pix_price' => $this->product->pix_price,
                'stock_notice' => $this->product->stock_notice,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
