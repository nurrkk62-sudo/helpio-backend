<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends BaseController
{
    /**
     * Menampilkan semua kategori.
     */
    public function index(): JsonResponse
    {
        $categories = Category::all();

        return $this->successResponse(
            $categories,
            'Daftar kategori berhasil diambil.'
        );
    }

    /**
     * Menyimpan kategori baru.
     */
    public function store(Request $request): JsonResponse
{
    try {

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        $category = Category::create($validated);

        return $this->successResponse(
            $category,
            'Kategori berhasil ditambahkan.',
            201
        );

    } catch (\Exception $e) {

        return response()->json([
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ],500);

    }
}

    /**
     * Menampilkan satu kategori.
     */
    public function show(Category $category): JsonResponse
    {
        return $this->successResponse(
            $category,
            'Detail kategori berhasil diambil.'
        );
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

        return $this->successResponse(
            $category,
            'Kategori berhasil diperbarui.'
        );
    }

    /**
     * Menghapus kategori.
     */
    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return $this->successResponse(
            null,
            'Kategori berhasil dihapus.'
        );
    }
}