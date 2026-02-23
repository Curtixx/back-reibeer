<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockRequest;
use App\Http\Requests\UpdateStockQuantityRequest;
use App\Http\Requests\UpdateStockRequest;
use App\Http\Resources\StockResource;
use App\Models\Stock;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class StockController extends Controller
{
    public function __construct(
        protected StockService $stockService
    ) {}

    /**
     * Clear all stocks cache.
     */
    private function clearStocksCache(): void
    {
        Cache::flush();
    }

    public function index(): JsonResponse
    {
        try {
            $cacheKey = 'stocks_page:'.request()->page.':per_page:'.request()->per_page;

            $stocks = Cache::remember($cacheKey, 600, function () {
                return Stock::select(['id', 'product_id', 'quantity', 'created_at', 'updated_at'])
                    ->with(['product:id,name'])
                    ->simplePaginate(request()->per_page);
            });

            return response()->json(StockResource::collection($stocks), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao buscar estoque!'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStockRequest $request): JsonResponse
    {
        try {
            $stocks = $this->stockService->createOrAddStocks(
                products: $request->validated('products'),
            );

            $this->clearStocksCache();

            return response()->json(StockResource::collection($stocks), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao criar estoque!'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Stock $stock): JsonResponse
    {
        try {
            return response()->json(new StockResource($stock), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Estoque não encontrado!'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStockRequest $request, Stock $stock): JsonResponse
    {
        try {
            $stock->update($request->validated());

            $this->clearStocksCache();

            return response()->json(new StockResource($stock), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar estoque!'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stock $stock): JsonResponse
    {
        try {
            $stock->delete();

            $this->clearStocksCache();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao deletar estoque!'], 500);
        }
    }

    /**
     * Update stock quantity (add or remove).
     */
    public function updateQuantity(UpdateStockQuantityRequest $request, Stock $stock): JsonResponse
    {
        try {
            $validated = $request->validated();

            if (data_get($validated, 'action') === 'add') {
                $stock = $this->stockService->addQuantity(
                    productId: data_get($stock, 'product_id'),
                    quantity: data_get($validated, 'quantity'),
                );
            } else {
                $stock = $this->stockService->removeQuantity(
                    productId: data_get($stock, 'product_id'),
                    quantity: data_get($validated, 'quantity'),
                );
            }

            $this->clearStocksCache();

            return response()->json(new StockResource($stock), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
