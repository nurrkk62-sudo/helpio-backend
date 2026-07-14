<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\OrderStateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends BaseController
{
    public function __construct(
        protected OrderService $service,
        protected OrderStateService $stateService
    ) {
    }

    /**
     * User membuat pesanan baru.
     */
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

    /**
     * Menampilkan pesanan milik user.
     */
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

    /**
     * Menampilkan pesanan milik expert.
     */
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

    /**
     * Mengubah status pesanan sesuai state machine.
     */
    public function updateStatus(
        UpdateOrderStatusRequest $request,
        Order $order
    ): JsonResponse {
        $order = $this->stateService->updateStatus(
            $request->user(),
            $order,
            $request->validated('status')
        );

        return $this->successResponse(
            $order,
            'Status pesanan berhasil diperbarui.'
        );
    }
}