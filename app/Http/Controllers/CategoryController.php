<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Menampilkan semua kategori.
     */
    public function index(): JsonResponse
    {
        $categories = Category::all();

        return response()->json($categories, 200);
    }

    /**
     * Menyimpan kategori baru.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        $category = Category::create($validated);

        return response()->json($category, 201);
    }

    /**
     * Menampilkan satu kategori.
     */
    public function show(Category $category): JsonResponse
    {
        return response()->json($category, 200);
    }

    /**
     * Mengubah data kategori.
     */
    public function update(
        Request $request,
        Category $category
    ): JsonResponse {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->update($validated);

        return response()->json($category, 200);
    }

    /**
     * Menghapus kategori.
     */
    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json(null, 204);
    }
}