<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProdutcRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        try {
            $produtos = Product::where('is_active', true)->get();
            return response()->json(ProductResource::collection($produtos), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao buscar produtos!'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        try {
            $product = DB::transaction(function () use ($request) {
                return Product::create($request->validated());
            });

            return response()->json(new ProductResource($product), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao criar produto!'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        try {
            return response()->json(new ProductResource($product), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Produto não encontrado!'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProdutcRequest $request, Product $product)
    {
        try {
            $product->update($request->validated());
            return response()->json(new ProductResource($product), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar produto!'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            DB::transaction(function () use ($product) {
                return $product->update(['is_active' => false]);
            });

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao deletar produto!'], 500);
        }
    }
}
