<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends BaseController
{
    public function __construct(
        protected OrderService $service
    ) {
    }

    public function store(
        StoreOrderRequest $request
    ): JsonResponse {
        $order = $this->service->create(
            $request->user(),
            $request->validated()
        );

        return $this->successResponse(
            $order,
            'Pesanan berhasil dibuat.',
            201
        );
    }

    public function userOrders(
        Request $request
    ): JsonResponse {
        $orders = $this->service->getByUser(
            $request->user()
        );

        return $this->successResponse(
            $orders,
            'Data pesanan user berhasil diambil.'
        );
    }

    public function expertOrders(
        Request $request
    ): JsonResponse {
        $orders = $this->service->getByExpert(
            $request->user()
        );

        return $this->successResponse(
            $orders,
            'Data pesanan expert berhasil diambil.'
        );
    }
}