<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class CategoryService
{
    /**
     * @param  array<int>  $ids
     */
    public function getAllCategories(int $page, int $perPage, array $ids = []): LengthAwarePaginator
    {
        $cacheKey = 'categories_page:'.$page.':per_page:'.$perPage.':filters:'.md5(serialize($ids));

        return Cache::tags(['categories'])->remember($cacheKey, 600, function () use ($perPage, $ids) {
            $query = Category::where('is_active', true);

            if (! empty($ids)) {
                $query->whereIn('id', $ids);
            }

            return $query->paginate($perPage);
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
