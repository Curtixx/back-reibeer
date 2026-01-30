<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProdutcRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function __construct(public ProductService $productService) {}

    public function index()
    {
        try {
            $produtos = $this->productService->getAllProducts();

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
                return $this->productService->createProduct($request->validated());
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
            $productFound = $this->productService->getProductById($product->id);

            if (! $productFound) {
                return response()->json(['error' => 'Produto não encontrado!'], 404);
            }

            return response()->json(new ProductResource($productFound), 200);
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
            $updatedProduct = DB::transaction(function () use ($request, $product) {
                return $this->productService->updateProduct($product, $request->validated());
            });

            return response()->json(new ProductResource($updatedProduct), 200);
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
                return $this->productService->deleteProduct($product);
            });

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao deletar produto!'], 500);
        }
    }
}
