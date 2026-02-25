<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProdutcRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function __construct(public ProductService $productService) {}

    private function clearProductsCache(): void
    {
        Cache::tags(['products'])->flush();
    }

    public function index()
    {
        try {
            $page = request()->input('page', 1);
            $perPage = request()->input('per_page', 15);
            $barCode = request()->input('bar_code');
            $produtos = $this->productService->getAllProducts($page, $perPage, $barCode);

            return ProductResource::collection($produtos);
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

            $this->clearProductsCache();

            return response()->json(new ProductResource($product), 201);
        } catch (\Exception $e) {
            dd($e);
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

            $this->clearProductsCache();

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

            $this->clearProductsCache();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao deletar produto!'], 500);
        }
    }
}
