<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function getAllCategories(): Collection
    {
        return Category::where('is_active', true)->get();
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
