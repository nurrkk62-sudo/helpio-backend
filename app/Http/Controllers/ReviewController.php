<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Expert;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;

class ReviewController extends BaseController
{
    public function __construct(
        protected ReviewService $service
    ) {
    }

    /**
     * User membuat review.
     */
    public function store(
        StoreReviewRequest $request
    ): JsonResponse {
        $review = $this->service->create(
            $request->user(),
            $request->validated()
        );

        return $this->successResponse(
            $review,
            'Review berhasil dibuat.',
            201
        );
    }

    /**
     * Menampilkan review berdasarkan expert.
     */
    public function byExpert(
        Expert $expert
    ): JsonResponse {
        $reviews = $expert->reviews()
            ->with([
                'user',
                'order',
            ])
            ->latest()
            ->get();

        return $this->successResponse(
            $reviews,
            'Data review expert berhasil diambil.'
        );
    }
}