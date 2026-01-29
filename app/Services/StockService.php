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
    public function addQuantity(int $productId, int $quantity, ?string $description = null): Stock
    {
        return DB::transaction(function () use ($productId, $quantity, $description) {
            $stock = Stock::firstOrCreate(
                ['product_id' => $productId],
                ['quantity' => 0]
            );

            $oldQuantity = $stock->quantity;
            $stock->increment('quantity', $quantity);
            $stock->refresh();

            if ($description) {
                $stock->update(['description' => $description]);
            }

            $this->logService->log(
                model: $stock,
                action: 'quantity_added',
                description: "Adicionado {$quantity} unidades ao estoque",
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
    public function removeQuantity(int $productId, int $quantity, ?string $description = null): Stock
    {
        return DB::transaction(function () use ($productId, $quantity, $description) {
            $stock = Stock::where('product_id', $productId)->firstOrFail();

            if ($stock->quantity < $quantity) {
                throw new \Exception('Quantidade insuficiente em estoque');
            }

            $previousQuantity = $stock->quantity;
            $stock->decrement('quantity', $quantity);
            $stock->refresh();

            if ($description) {
                $stock->update(['description' => $description]);
            }

            $this->logService->log(
                model: $stock,
                action: 'quantity_removed',
                description: "Removido {$quantity} unidades do estoque",
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
    public function createOrAddStocks(array $products, ?string $description = null): \Illuminate\Support\Collection
    {
        return DB::transaction(function () use ($products, $description) {
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
                        description: $description
                    );
                } else {
                    $stock = Stock::create([
                        'product_id' => $productId,
                        'quantity' => $quantity,
                        'description' => $description,
                    ]);
                }

                $stocks->push($stock);
            }

            return $stocks;
        });
    }
}
