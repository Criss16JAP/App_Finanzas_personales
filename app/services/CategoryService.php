<?php

namespace App\Services;

use App\Models\Category;
use App\Models\User;

class CategoryService
{
    public function getCategoriesForUser(User $user)
    {
        return $user->categories()->orderBy('type')->orderBy('name')->get();
    }

    public function createCategory(User $user, array $data)
    {
        return $user->categories()->create($data);
    }

    public function updateCategory(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    public function deleteCategory(Category $category): ?bool
    {
        return $category->delete();
    }
}
