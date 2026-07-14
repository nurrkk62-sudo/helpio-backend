<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReviewService
{
    public function create(
        User $user,
        array $data
    ): Review {
        if ($user->role !== 'user') {
            throw ValidationException::withMessages([
                'user' => [
                    'Hanya user yang dapat memberikan review.',
                ],
            ]);
        }

        $order = Order::query()
            ->whereKey($data['order_id'])
            ->firstOrFail();

        if ($order->user_id !== $user->id) {
            throw ValidationException::withMessages([
                'order_id' => [
                    'Anda tidak memiliki akses ke pesanan ini.',
                ],
            ]);
        }

        if ($order->status !== 'Closed') {
            throw ValidationException::withMessages([
                'order_id' => [
                    'Review hanya dapat diberikan setelah pesanan selesai.',
                ],
            ]);
        }

        if (
            Review::query()
                ->where('order_id', $order->id)
                ->exists()
        ) {
            throw ValidationException::withMessages([
                'order_id' => [
                    'Pesanan ini sudah memiliki review.',
                ],
            ]);
        }

        return DB::transaction(function () use (
            $user,
            $order,
            $data
        ): Review {
            $review = Review::create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'expert_id' => $order->expert_id,
                'rating' => $data['rating'],
                'comment' => $data['comment'] ?? null,
            ]);

            $rating = Review::query()
                ->where('expert_id', $order->expert_id)
                ->avg('rating');

            $reviewCount = Review::query()
                ->where('expert_id', $order->expert_id)
                ->count();

            $order->expert()->update([
                'rating' => round(
                    (float) $rating,
                    1
                ),
                'review_count' => $reviewCount,
            ]);

            return $review->load([
                'user',
                'expert.user',
                'order',
            ]);
        });
    }
}