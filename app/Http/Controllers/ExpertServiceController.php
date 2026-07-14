<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpertServiceRequest;
use App\Http\Requests\UpdateExpertServiceRequest;
use App\Models\Expert;
use App\Models\ExpertService;
use App\Services\ExpertOfferingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpertServiceController extends BaseController
{
    public function __construct(
        protected ExpertOfferingService $service
    ) {
    }

    /**
     * Menampilkan semua layanan expert aktif.
     */
    public function index(): JsonResponse
    {
        $services = $this->service
            ->getAll()
            ->where('is_active', true)
            ->values();

        return $this->successResponse(
            $services,
            'Data layanan expert berhasil diambil.'
        );
    }

    /**
     * Menampilkan layanan berdasarkan expert.
     */
    public function byExpert(
        Expert $expert
    ): JsonResponse {
        $services = $this->service
            ->getByExpert($expert);

        return $this->successResponse(
            $services,
            'Data layanan expert berhasil diambil.'
        );
    }

    /**
     * Expert membuat layanan baru.
     */
    public function store(
        StoreExpertServiceRequest $request
    ): JsonResponse {
        $service = $this->service->create(
            $request->user(),
            $request->validated()
        );

        return $this->successResponse(
            $service,
            'Layanan expert berhasil dibuat.',
            201
        );
    }

    /**
     * Menampilkan satu layanan.
     */
    public function show(
        ExpertService $expertService
    ): JsonResponse {
        $expertService->load([
            'expert.user',
            'expert.category',
        ]);

        return $this->successResponse(
            $expertService,
            'Detail layanan expert berhasil diambil.'
        );
    }

    /**
     * Expert mengubah layanan miliknya.
     */
    public function update(
        UpdateExpertServiceRequest $request,
        ExpertService $expertService
    ): JsonResponse {
        $service = $this->service->update(
            $request->user(),
            $expertService,
            $request->validated()
        );

        return $this->successResponse(
            $service,
            'Layanan expert berhasil diperbarui.'
        );
    }

    /**
     * Expert menghapus layanan miliknya.
     */
    public function destroy(
        Request $request,
        ExpertService $expertService
    ): JsonResponse {
        $this->service->delete(
            $request->user(),
            $expertService
        );

        return $this->successResponse(
            null,
            'Layanan expert berhasil dihapus.'
        );
    }
}