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

    public function createDefaultCategories(User $user): void
    {
        $defaultCategories = [
            'egress' => [
                'Hogar',
                'Servicios',
                'Alimentación',
                'Transporte',
                'Pago de Créditos',
                'Salud',
                'Ocio',
                'Préstamos',
                'Otros Gastos'
            ],
            'income' => [
                'Salario',
                'Bonificaciones',
                'Ingresos Extra',
                'Inversiones',
                'Préstamos',
                'Otros Ingresos'
            ]
        ];

        foreach ($defaultCategories['egress'] as $categoryName) {
            $user->categories()->firstOrCreate(
                ['name' => $categoryName, 'type' => 'egress']
            );
        }

        foreach ($defaultCategories['income'] as $categoryName) {
            $user->categories()->firstOrCreate(
                ['name' => $categoryName, 'type' => 'income']
            );
        }
    }
}
