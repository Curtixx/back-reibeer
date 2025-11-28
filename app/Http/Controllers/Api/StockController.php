<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockRequest;
use App\Http\Requests\UpdateStockRequest;
use App\Http\Resources\StockResource;
use App\Models\Stock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $stocks = Stock::with('product')->get();
            return response()->json(new StockResource($stocks), 200);
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
            $stock = DB::transaction(function () use ($request) {
                return Stock::create($request->validated());
            });
            return response()->json(new StockResource($stock), 200);
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
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao deletar estoque!'], 500);
        }
    }
}
