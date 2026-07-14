<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminUpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends BaseController
{
    public function __construct(
        protected AdminService $service
    ) {
    }

    public function dashboard(): JsonResponse
    {
        return $this->successResponse(
            $this->service->dashboard(),
            'Data dashboard admin berhasil diambil.'
        );
    }

    public function statistics(): JsonResponse
    {
        return $this->successResponse(
            $this->service->statistics(),
            'Data statistik HelpIO berhasil diambil.'
        );
    }

    public function users(
        Request $request
    ): JsonResponse {
        return $this->successResponse(
            $this->service->users(
                $request->only([
                    'role',
                    'search',
                ])
            ),
            'Data user berhasil diambil.'
        );
    }

    public function experts(
        Request $request
    ): JsonResponse {
        return $this->successResponse(
            $this->service->experts(
                $request->only([
                    'verified',
                    'verification_status',
                    'search',
                ])
            ),
            'Data expert berhasil diambil.'
        );
    }

    public function orders(
        Request $request
    ): JsonResponse {
        return $this->successResponse(
            $this->service->orders(
                $request->only([
                    'status',
                    'search',
                ])
            ),
            'Data pesanan berhasil diambil.'
        );
    }

    public function orderDetail(
        Order $order
    ): JsonResponse {
        return $this->successResponse(
            $this->service->orderDetail($order),
            'Detail pesanan berhasil diambil.'
        );
    }

    public function updateOrderStatus(
        AdminUpdateOrderStatusRequest $request,
        Order $order
    ): JsonResponse {
        return $this->successResponse(
            $this->service->updateOrderStatus(
                $order,
                $request->validated()
            ),
            'Status pesanan berhasil diperbarui oleh admin.'
        );
    }

    public function verifications(
        Request $request
    ): JsonResponse {
        return $this->successResponse(
            $this->service->verifications(
                $request->only([
                    'status',
                ])
            ),
            'Data verifikasi expert berhasil diambil.'
        );
    }
}