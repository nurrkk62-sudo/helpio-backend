<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewExpertVerificationRequest;
use App\Http\Requests\StoreExpertVerificationRequest;
use App\Models\ExpertVerification;
use App\Services\ExpertVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpertVerificationController extends BaseController
{
    public function __construct(
        protected ExpertVerificationService $service
    ) {
    }

    public function store(
        StoreExpertVerificationRequest $request
    ): JsonResponse {
        $verification = $this->service->submit(
            $request->user(),
            $request->validated()
        );

        return $this->successResponse(
            $verification,
            'Pengajuan verifikasi expert berhasil dikirim.',
            201
        );
    }

    public function me(
        Request $request
    ): JsonResponse {
        $verification = $this->service
            ->getMyVerification(
                $request->user()
            );

        return $this->successResponse(
            $verification,
            'Data verifikasi expert berhasil diambil.'
        );
    }

    public function pending(): JsonResponse
    {
        $verifications = $this->service->pending();

        return $this->successResponse(
            $verifications,
            'Data pengajuan verifikasi pending berhasil diambil.'
        );
    }

    public function review(
        ReviewExpertVerificationRequest $request,
        ExpertVerification $expertVerification
    ): JsonResponse {
        $verification = $this->service->review(
            $request->user(),
            $expertVerification,
            $request->validated()
        );

        return $this->successResponse(
            $verification,
            'Verifikasi expert berhasil diproses.'
        );
    }
}