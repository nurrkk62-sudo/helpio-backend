<?php

namespace App\Services;

use App\Models\Expert;
use App\Models\ExpertVerification;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AdminService
{
    public function dashboard(): array
    {
        return [
            'users' => [
                'total' => User::query()->count(),
                'customers' => User::query()
                    ->where('role', 'user')
                    ->count(),
                'experts' => User::query()
                    ->where('role', 'expert')
                    ->count(),
                'admins' => User::query()
                    ->where('role', 'admin')
                    ->count(),
            ],

            'experts' => [
                'total' => Expert::query()->count(),
                'verified' => Expert::query()
                    ->where('verified', true)
                    ->count(),
                'pending_verification' => Expert::query()
                    ->where('verification_status', 'pending')
                    ->count(),
            ],

            'orders' => [
                'total' => Order::query()->count(),
                'pending' => Order::query()
                    ->where('status', 'Pending')
                    ->count(),
                'accepted' => Order::query()
                    ->where('status', 'Diterima')
                    ->count(),
                'completed' => Order::query()
                    ->where('status', 'Selesai')
                    ->count(),
                'review' => Order::query()
                    ->where('status', 'Review')
                    ->count(),
                'closed' => Order::query()
                    ->where('status', 'Closed')
                    ->count(),
                'cancelled' => Order::query()
                    ->where('status', 'Dibatalkan')
                    ->count(),
            ],

            'reviews' => [
                'total' => Review::query()->count(),
                'average_rating' => round(
                    (float) Review::query()->avg('rating'),
                    2
                ),
            ],

            'verifications' => [
                'pending' => ExpertVerification::query()
                    ->where('status', 'pending')
                    ->count(),
                'approved' => ExpertVerification::query()
                    ->where('status', 'approved')
                    ->count(),
                'rejected' => ExpertVerification::query()
                    ->where('status', 'rejected')
                    ->count(),
            ],
        ];
    }

    public function statistics(): array
    {
        return [
            'revenue' => [
                'total_closed_order_value' => (float) Order::query()
                    ->where('status', 'Closed')
                    ->sum('price'),

                'average_order_value' => round(
                    (float) Order::query()
                        ->where('status', 'Closed')
                        ->avg('price'),
                    2
                ),
            ],

            'order_performance' => [
                'total_orders' => Order::query()->count(),

                'closed_orders' => Order::query()
                    ->where('status', 'Closed')
                    ->count(),

                'cancelled_orders' => Order::query()
                    ->where('status', 'Dibatalkan')
                    ->count(),

                'completion_rate' => $this->completionRate(),
            ],

            'expert_performance' => [
                'total_experts' => Expert::query()->count(),

                'verified_experts' => Expert::query()
                    ->where('verified', true)
                    ->count(),

                'average_rating' => round(
                    (float) Expert::query()->avg('rating'),
                    2
                ),

                'total_completed_jobs' => (int) Expert::query()
    ->sum('completed_jobs'),
            ],

            'review_performance' => [
                'total_reviews' => Review::query()->count(),

                'average_rating' => round(
                    (float) Review::query()->avg('rating'),
                    2
                ),

                'five_star_reviews' => Review::query()
                    ->where('rating', 5)
                    ->count(),
            ],
        ];
    }

    private function completionRate(): float
    {
        $totalOrders = Order::query()->count();

        if ($totalOrders === 0) {
            return 0;
        }

        $closedOrders = Order::query()
            ->where('status', 'Closed')
            ->count();

        return round(
            ($closedOrders / $totalOrders) * 100,
            2
        );
    }

    public function users(
        array $filters
    ): LengthAwarePaginator {
        return User::query()
            ->when(
                $filters['role'] ?? null,
                function ($query, $role) {
                    $query->where('role', $role);
                }
            )
            ->when(
                $filters['search'] ?? null,
                function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere(
                                'email',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'phone',
                                'like',
                                "%{$search}%"
                            );
                    });
                }
            )
            ->latest()
            ->paginate(15);
    }

    public function experts(
        array $filters
    ): LengthAwarePaginator {
        return Expert::query()
            ->with([
                'user',
                'category',
                'verification',
            ])
            ->when(
                isset($filters['verified']),
                function ($query) use ($filters) {
                    $query->where(
                        'verified',
                        filter_var(
                            $filters['verified'],
                            FILTER_VALIDATE_BOOLEAN
                        )
                    );
                }
            )
            ->when(
                $filters['verification_status'] ?? null,
                function ($query, $status) {
                    $query->where(
                        'verification_status',
                        $status
                    );
                }
            )
            ->when(
                $filters['search'] ?? null,
                function ($query, $search) {
                    $query->whereHas(
                        'user',
                        function ($query) use ($search) {
                            $query
                                ->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'email',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )
            ->latest()
            ->paginate(15);
    }

    public function orders(
        array $filters
    ): LengthAwarePaginator {
        return Order::query()
            ->with([
                'user',
                'expert.user',
                'expert.category',
            ])
            ->when(
                $filters['status'] ?? null,
                function ($query, $status) {
                    $query->where('status', $status);
                }
            )
            ->when(
                $filters['search'] ?? null,
                function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query
                            ->where('id', 'like', "%{$search}%")
                            ->orWhere(
                                'service_title',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'address',
                                'like',
                                "%{$search}%"
                            );
                    });
                }
            )
            ->latest()
            ->paginate(15);
    }

    public function orderDetail(
        Order $order
    ): Order {
        return $order->load([
            'user',
            'expert.user',
            'expert.category',
            'review',
        ]);
    }

    public function updateOrderStatus(
        Order $order,
        array $data
    ): Order {
        return DB::transaction(function () use (
            $order,
            $data
        ): Order {
            $oldStatus = $order->status;
            $newStatus = $data['status'];

            $order->update([
                'status' => $newStatus,
            ]);

            if (
                $oldStatus !== 'Closed' &&
                $newStatus === 'Closed'
            ) {
                $order->expert()->increment(
                    'completed_jobs'
                );
            }

            if (
                $oldStatus === 'Closed' &&
                $newStatus !== 'Closed'
            ) {
                $order->expert()
                    ->where('completed_jobs', '>', 0)
                    ->decrement('completed_jobs');
            }

            return $order->fresh([
                'user',
                'expert.user',
                'expert.category',
                'review',
            ]);
        });
    }

    public function verifications(
        array $filters
    ): LengthAwarePaginator {
        return ExpertVerification::query()
            ->with([
                'expert.user',
                'expert.category',
                'reviewer',
            ])
            ->when(
                $filters['status'] ?? null,
                function ($query, $status) {
                    $query->where('status', $status);
                }
            )
            ->latest()
            ->paginate(15);
    }
}