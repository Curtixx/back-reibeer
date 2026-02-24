<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class CategoryService
{
    public function getAllCategories(int $page, int $perPage): LengthAwarePaginator
    {
        $cacheKey = 'categories_page:' . $page . ':per_page:' . $perPage;
        return Cache::tags(['categories'])->remember($cacheKey, 600, function () use ($page, $perPage) {
            return Category::where('is_active', true)->paginate($perPage);
        });
    }

    public function getCategoryById(int $id): ?Category
    {
        return Category::where('id', $id)
            ->where('is_active', true)
            ->first();
    }

    public function createCategory(array $data): Category
    {
        return Category::create([
            'name' => $data['name'],
            'is_active' => true,
        ]);
    }

    public function updateCategory(Category $category, array $data): Category
    {
        $category->update([
            'name' => $data['name'] ?? $category->name,
        ]);

        return $category->fresh();
    }

    public function deleteCategory(Category $category): bool
    {
        return $category->update(['is_active' => false]);
    }
}
