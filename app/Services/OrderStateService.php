<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class OrderStateService
{
    private const TRANSITIONS = [
        'Pending' => [
            'Diterima',
            'Cancelled',
        ],

        'Diterima' => [
            'Dalam Proses',
            'Cancelled',
        ],

        'Dalam Proses' => [
            'Selesai',
        ],

        'Selesai' => [
            'Review',
        ],

        'Review' => [
            'Closed',
        ],

        'Closed' => [],

        'Cancelled' => [],
    ];

    public function updateStatus(
        User $user,
        Order $order,
        string $newStatus
    ): Order {
        $this->ensureActorCanChangeStatus(
            $user,
            $order,
            $newStatus
        );

        $this->ensureValidTransition(
            $order,
            $newStatus
        );

        $order->update([
            'status' => $newStatus,
        ]);

        if ($newStatus === 'Closed') {
            $order->expert()->increment(
                'completed_jobs'
            );
        }

        return $order->fresh([
            'user',
            'expert.user',
            'expert.category',
        ]);
    }

    private function ensureValidTransition(
        Order $order,
        string $newStatus
    ): void {
        $allowedTransitions = self::TRANSITIONS[
            $order->status
        ] ?? [];

        if (
            ! in_array(
                $newStatus,
                $allowedTransitions,
                true
            )
        ) {
            throw ValidationException::withMessages([
                'status' => [
                    sprintf(
                        'Perubahan status dari %s ke %s tidak diizinkan.',
                        $order->status,
                        $newStatus
                    ),
                ],
            ]);
        }
    }

    private function ensureActorCanChangeStatus(
        User $user,
        Order $order,
        string $newStatus
    ): void {
        if ($user->role === 'expert') {
            $expert = $user->expert;

            if (
                ! $expert ||
                $order->expert_id !== $expert->id
            ) {
                $this->throwAccessDenied();
            }

            if (
                ! in_array(
                    $newStatus,
                    [
                        'Diterima',
                        'Dalam Proses',
                        'Selesai',
                        'Cancelled',
                    ],
                    true
                )
            ) {
                $this->throwAccessDenied();
            }

            return;
        }

        if ($user->role === 'user') {
            if ($order->user_id !== $user->id) {
                $this->throwAccessDenied();
            }

            if (
                ! in_array(
                    $newStatus,
                    [
                        'Review',
                        'Closed',
                    ],
                    true
                )
            ) {
                $this->throwAccessDenied();
            }

            return;
        }

        $this->throwAccessDenied();
    }

    private function throwAccessDenied(): never
    {
        throw ValidationException::withMessages([
            'order' => [
                'Anda tidak memiliki akses untuk mengubah status pesanan ini.',
            ],
        ]);
    }
}