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
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->service->all(),
            'message' => 'Berhasil menarik semua data ahli.',
        ], 200);
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