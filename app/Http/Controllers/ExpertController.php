<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpertRequest;
use App\Http\Requests\UpdateExpertRequest;
use App\Models\Expert;
use App\Services\ExpertService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpertController extends BaseController
{
    protected ExpertService $service;

    public function __construct(ExpertService $service)
    {
        $this->service = $service;
    }

    /**
     * Menampilkan semua expert.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Expert::query()
            ->with([
                'user',
                'category',
            ]);

        if ($request->filled('category_id')) {
            $query->where(
                'category_id',
                $request->input('category_id')
            );
        }

        if ($request->filled('location')) {
            $query->where(
                'location',
                'like',
                '%' . $request->input('location') . '%'
            );
        }

        if ($request->filled('min_rating')) {
            $query->where(
                'rating',
                '>=',
                $request->input('min_rating')
            );
        }

        if ($request->filled('verified')) {
            $verified = filter_var(
                $request->input('verified'),
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            );

            if ($verified !== null) {
                $query->where(
                    'verified',
                    $verified
                );
            }
        }

        $experts = $query
            ->latest()
            ->get();

        return $this->successResponse(
            $experts,
            'Data ahli berhasil diambil.'
        );
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

        return $this->successResponse(
            $expert,
            'Data ahli berhasil dibuat.',
            201
        );
    }

    /**
     * Menampilkan satu expert.
     */
    public function show(int $id): JsonResponse
    {
        $expert = Expert::query()
            ->with([
                'user',
                'category',
            ])
            ->find($id);

        if (!$expert) {
            return $this->errorResponse(
                'Data ahli tidak ditemukan.',
                404
            );
        }

        return $this->successResponse(
            $expert,
            'Berhasil menarik satu data ahli.'
        );
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

            return $this->successResponse(
                $expert,
                'Data ahli berhasil diperbarui.'
            );
        } catch (\Exception $exception) {
            return $this->errorResponse(
                'Data ahli tidak ditemukan.',
                404
            );
        }
    }

    /**
     * Menghapus expert.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->delete($id);

            return $this->successResponse(
                null,
                'Data ahli berhasil dihapus.'
            );
        } catch (\Exception $exception) {
            return $this->errorResponse(
                'Data ahli tidak ditemukan.',
                404
            );
        }
    }
}