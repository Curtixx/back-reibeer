<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function __construct(public CategoryService $categoryService) {}

    /**
     * Clear all categories cache.
     */
    private function clearCategoriesCache(): void
    {
        Cache::tags(['categories'])->flush();
    }

    public function index(): AnonymousResourceCollection|JsonResponse
    {
        try {
            $page = request()->input('page', 1);
            $perPage = request()->input('per_page', 15);
            $categories = $this->categoryService->getAllCategories($page, $perPage);

            return CategoryResource::collection($categories);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao buscar categorias!'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        try {
            $category = DB::transaction(function () use ($request) {
                return $this->categoryService->createCategory($request->validated());
            });

            $this->clearCategoriesCache();

            return response()->json(new CategoryResource($category), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao criar categoria!'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        try {
            $categoryFound = $this->categoryService->getCategoryById($category->id);

            if (! $categoryFound) {
                return response()->json(['error' => 'Categoria não encontrada!'], 404);
            }

            return response()->json(new CategoryResource($categoryFound), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Categoria não encontrada!'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        try {
            $updatedCategory = DB::transaction(function () use ($request, $category) {
                return $this->categoryService->updateCategory($category, $request->validated());
            });

            $this->clearCategoriesCache();

            return response()->json(new CategoryResource($updatedCategory), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar categoria!'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {
            DB::transaction(function () use ($category) {
                return $this->categoryService->deleteCategory($category);
            });

            $this->clearCategoriesCache();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao deletar categoria!'], 500);
        }
    }
}
