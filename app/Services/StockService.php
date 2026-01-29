<?php

namespace App\Services;

use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function __construct(
        protected LogService $logService
    ) {}

    /**
     * Add quantity to stock.
     */
    public function addQuantity(int $productId, int $quantity): Stock
    {
        return DB::transaction(function () use ($productId, $quantity) {
            $stock = Stock::firstOrCreate(
                ['product_id' => $productId],
                ['quantity' => 0]
            );

            $oldQuantity = $stock->quantity;
            $stock->increment('quantity', $quantity);
            $stock->refresh();

            $this->logService->log(
                model: $stock,
                action: 'quantity_added',
                description: "Adicionado {$quantity} unidades ao estoque do produto ID {$productId}",
                columnName: 'quantity',
                oldValue: $oldQuantity,
                newValue: $stock->quantity
            );

            return $stock;
        });
    }

    /**
     * Remove quantity from stock.
     */
    public function removeQuantity(int $productId, int $quantity): Stock
    {
        return DB::transaction(function () use ($productId, $quantity) {
            $stock = Stock::where('product_id', $productId)->firstOrFail();

            if ($stock->quantity < $quantity) {
                throw new \Exception('Quantidade insuficiente em estoque');
            }

            $previousQuantity = $stock->quantity;
            $stock->decrement('quantity', $quantity);
            $stock->refresh();

            $this->logService->log(
                model: $stock,
                action: 'quantity_removed',
                description: "Removido {$quantity} unidades do estoque do produto ID {$productId}",
                columnName: 'quantity',
                oldValue: $previousQuantity,
                newValue: $stock->quantity
            );

            return $stock;
        });
    }

    /**
     * Create or add stocks for multiple products.
     */
    public function createOrAddStocks(array $products): \Illuminate\Support\Collection
    {
        return DB::transaction(function () use ($products) {
            $productIds = collect($products)->pluck('id')->toArray();
            $existingStocks = Stock::whereIn('product_id', $productIds)->get()->keyBy('product_id');

            $stocks = collect();

            foreach ($products as $product) {
                $productId = data_get($product, 'id');
                $quantity = data_get($product, 'quantity');

                $existingStock = $existingStocks->get($productId);

                if ($existingStock) {
                    $stock = $this->addQuantity(
                        productId: $productId,
                        quantity: $quantity,
                    );
                } else {
                    $stock = Stock::create([
                        'product_id' => $productId,
                        'quantity' => $quantity,
                    ]);
                }

                $stocks->push($stock);
            }

            return $stocks;
        });
    }
}
