<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class ProductService
{
    public function getAllProducts(int $page, int $perPage, ?string $barCode = null, array $ids = []): LengthAwarePaginator
    {
        $cacheKey = 'products_page:'.$page.':per_page:'.$perPage.':barcode:'.($barCode ?? 'none').':ids:'.md5(serialize($ids));

        return Cache::tags(['products'])->remember($cacheKey, 600, function () use ($perPage, $barCode, $ids) {
            $query = Product::where('is_active', true)->with('categories');

            if ($barCode) {
                $query->where('bar_code', $barCode);
            }

            if (! empty($ids)) {
                $query->whereIn('id', $ids);
            }

            return $query->paginate($perPage);
        });
    }

    public function getProductById(int $id): ?Product
    {
        return Product::where('id', $id)
            ->where('is_active', true)
            ->with('categories')
            ->first();
    }

    public function createProduct(array $data): Product
    {
        $product = Product::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'sale_price' => $data['sale_price'],
            'cost_price' => $data['cost_price'],
            'pix_price' => $data['pix_price'] ?? null,
            'stock_notice' => $data['stock_notice'],
            'bar_code' => $data['bar_code'] ?? null,
        ]);

        if (isset($data['categories']) && is_array($data['categories'])) {
            $product->categories()->sync($data['categories']);
        }

        return $product->fresh('categories');
    }

    public function updateProduct(Product $product, array $data): Product
    {
        $product->update([
            'name' => $data['name'] ?? $product->name,
            'description' => $data['description'] ?? $product->description,
            'sale_price' => $data['sale_price'] ?? $product->sale_price,
            'cost_price' => $data['cost_price'] ?? $product->cost_price,
            'pix_price' => $data['pix_price'] ?? $product->pix_price,
            'stock_notice' => $data['stock_notice'] ?? $product->stock_notice,
            'is_active' => $data['is_active'] ?? $product->is_active,
            'bar_code' => $data['bar_code'] ?? $product->bar_code,
        ]);

        if (isset($data['categories']) && is_array($data['categories'])) {
            $product->categories()->sync($data['categories']);
        }

        return $product->fresh('categories');
    }

    public function deleteProduct(Product $product): bool
    {
        return $product->update(['is_active' => false]);
    }

    public function sendLowStockNotifications()
    {
        $lowStockProducts = Product::query()
            ->where('is_active', true)
            ->whereHas('stock', function ($query) {
                $query->whereColumn('quantity', '<=', 'stock_notice');
            })
            ->get();

        foreach ($lowStockProducts as $product) {
            Notification::create([
                'type' => 'low_stock',
                'message' => 'ATENÇÃO: O estoque do produto '.$product->name.' está baixo.',
                'product_id' => $product->id,
            ]);
        }
    }
}
