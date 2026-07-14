<?php

namespace App\Services;

use App\Models\ExpertService;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function create(
        User $user,
        array $data
    ): Order {
        if ($user->role !== 'user') {
            throw ValidationException::withMessages([
                'user' => [
                    'Hanya user yang dapat membuat pesanan.',
                ],
            ]);
        }

        $service = ExpertService::query()
            ->whereKey($data['expert_service_id'])
            ->where('is_active', true)
            ->first();

        if (! $service) {
            throw ValidationException::withMessages([
                'expert_service_id' => [
                    'Layanan expert tidak tersedia.',
                ],
            ]);
        }

        return DB::transaction(function () use (
            $user,
            $service,
            $data
        ): Order {
            $order = Order::create([
                'id' => $this->generateOrderId(),
                'user_id' => $user->id,
                'expert_id' => $service->expert_id,
                'service_title' => $service->name,
                'price' => $service->price,
                'address' => $data['address'],
                'date' => $data['date'],
                'time' => $data['time'],
                'description' => $data['description'],
                'photo_url' => $data['photo_url'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => 'Pending',
            ]);

            return $order->load([
                'user',
                'expert.user',
                'expert.category',
            ]);
        });
    }

    public function getByUser(
        User $user
    ): Collection {
        return Order::query()
            ->where('user_id', $user->id)
            ->with([
                'expert.user',
                'expert.category',
            ])
            ->latest()
            ->get();
    }

    public function getByExpert(
        User $user
    ): Collection {
        if ($user->role !== 'expert') {
            throw ValidationException::withMessages([
                'expert' => [
                    'Hanya expert yang dapat melihat pesanan expert.',
                ],
            ]);
        }

        $expert = $user->expert;

        if (! $expert) {
            throw ValidationException::withMessages([
                'expert' => [
                    'Profil expert tidak ditemukan.',
                ],
            ]);
        }

        return Order::query()
            ->where('expert_id', $expert->id)
            ->with([
                'user',
                'expert.user',
                'expert.category',
            ])
            ->latest()
            ->get();
    }

    private function generateOrderId(): string
    {
        do {
            $id = 'ORD-' . random_int(1000, 9999);
        } while (
            Order::query()
                ->whereKey($id)
                ->exists()
        );

        return $id;
    }
}