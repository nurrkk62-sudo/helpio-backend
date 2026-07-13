<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpertRequest;
use App\Http\Requests\UpdateExpertRequest;
use App\Services\ExpertService;
use Illuminate\Http\JsonResponse;

class ExpertController extends Controller
{
    protected ExpertService $service;

    public function __construct(ExpertService $service)
    {
        $this->service = $service;
    }

    /**
     * Menampilkan semua expert.
     */
    public function index(Request $request) 
{
    // 1. Mulai dengan Query Builder (belum mengambil data ke DB)
    $query = Expert::with(['user', 'category']);

    // 2. Cek apakah ada parameter 'category_id' di URL
    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    // 3. Tambahkan filter lainnya sesuai kebutuhan dokumentasi Anda
    if ($request->filled('location')) {
        $query->where('location', 'like', '%' . $request->location . '%');
    }

    if ($request->filled('min_rating')) {
        $query->where('rating', '>=', $request->min_rating);
    }

    if ($request->filled('verified')) {
        // filter boolean (true/false)
        $query->where('verified', filter_var($request->verified, FILTER_VALIDATE_BOOLEAN));
    }

    // 4. Ambil datanya setelah semua filter diterapkan
    $experts = $query->get();

    // 5. Kembalikan respons menggunakan BaseController
    return $this->success($experts);
}

    /**
     * Menyimpan expert baru.
     */
    public function store(
        StoreExpertRequest $request
    ): JsonResponse {
        $expert = $this->service->create(
            $request->validated()
        );

        return response()->json([
            'status' => 'success',
            'data' => $expert,
            'message' => 'Data ahli berhasil dibuat.',
        ], 201);
    }

    /**
     * Menampilkan satu expert.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $expert = $this->service->find($id);

            return response()->json([
                'status' => 'success',
                'data' => $expert,
                'message' => 'Berhasil menarik satu data ahli.',
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'data' => null,
                'message' => 'Data ahli tidak ditemukan.',
            ], 404);
        }
    }

    /**
     * Mengubah data expert.
     */
    public function update(
        UpdateExpertRequest $request,
        int $id
    ): JsonResponse {
        try {
            $expert = $this->service->update(
                $id,
                $request->validated()
            );

            return response()->json([
                'status' => 'success',
                'data' => $expert,
                'message' => 'Data ahli berhasil diperbarui.',
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'data' => null,
                'message' => 'Data ahli tidak ditemukan.',
            ], 404);
        }
    }

    /**
     * Menghapus expert.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->delete($id);

            return response()->json([
                'status' => 'success',
                'data' => null,
                'message' => 'Data ahli berhasil dihapus.',
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'data' => null,
                'message' => 'Data ahli tidak ditemukan.',
            ], 404);
        }
    }
}