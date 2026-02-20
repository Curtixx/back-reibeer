<?php

namespace App\Services;

use App\DTOS\StoreItemsSaleDTO;
use App\DTOS\StoreSaleDTO;
use App\Models\Product;
use App\Models\Sale;

class SaleService
{
    public function createSale(StoreSaleDTO $saleDTO): Sale
    {
        $sale = Sale::create([
            'payment_method' => $saleDTO->payment_method,
            'total_amount' => $saleDTO->total,
            'cashier_id' => $saleDTO->id_cashier,
        ]);

        return $sale;
    }

    public function createItemsSale(StoreItemsSaleDTO $itemsSaleDTO): void
    {
        $sale = Sale::findOrFail($itemsSaleDTO->sale_id);
        $product = Product::findOrFail($itemsSaleDTO->product_id);

        $sale->items()->create([
            'product_id' => $product->id,
            'quantity' => $itemsSaleDTO->quantity,
            'unit_price' => $sale->payment_method === 'PIX' ? $product->pix_price : $product->sale_price,
        ]);
    }
}
