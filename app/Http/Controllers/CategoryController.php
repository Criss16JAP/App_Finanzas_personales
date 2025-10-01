<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $categoryService)
    {
    }

    public function index()
    {
        $categories = $this->categoryService->getCategoriesForUser(Auth::user());
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::in(['income', 'egress'])],
        ]);

        $this->categoryService->createCategory(Auth::user(), $validatedData);
        return back()->with('success', '¡Categoría creada exitosamente!');
    }

    public function edit(Category $category)
    {
        if (Auth::id() !== $category->user_id) {
            abort(403);
        }
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        if (Auth::id() !== $category->user_id) {
            abort(403);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::in(['income', 'egress'])],
        ]);

        $this->categoryService->updateCategory($category, $validatedData);
        return redirect()->route('categories.index')->with('success', '¡Categoría actualizada!');
    }

    public function destroy(Category $category)
    {
        if (Auth::id() !== $category->user_id) {
            abort(403);
        }

        $this->categoryService->deleteCategory($category);
        return back()->with('success', '¡Categoría eliminada!');
    }
}
