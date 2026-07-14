<?php

namespace App\Services;

use App\Models\Expert;
use App\Models\ExpertService;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class ExpertOfferingService
{
    public function getAll(): Collection
    {
        return ExpertService::with([
            'expert.user',
            'expert.category',
        ])->get();
    }

    public function getByExpert(Expert $expert): Collection
    {
        return $expert->services()
            ->latest()
            ->get();
    }

    public function create(
        User $user,
        array $data
    ): ExpertService {
        $expert = $this->getOwnedExpert($user);

        $data['expert_id'] = $expert->id;

        return ExpertService::create($data)
            ->load([
                'expert.user',
                'expert.category',
            ]);
    }

    public function update(
        User $user,
        ExpertService $expertService,
        array $data
    ): ExpertService {
        $this->ensureOwnership(
            $user,
            $expertService
        );

        $expertService->update($data);

        return $expertService->fresh([
            'expert.user',
            'expert.category',
        ]);
    }

    public function delete(
        User $user,
        ExpertService $expertService
    ): void {
        $this->ensureOwnership(
            $user,
            $expertService
        );

        $expertService->delete();
    }

    private function getOwnedExpert(User $user): Expert
    {
        if (
            $user->role !== 'expert' ||
            $user->expert === null
        ) {
            throw ValidationException::withMessages([
                'expert' => [
                    'User bukan expert yang valid.',
                ],
            ]);
        }

        return $user->expert;
    }

    private function ensureOwnership(
        User $user,
        ExpertService $expertService
    ): void {
        $expert = $this->getOwnedExpert($user);

        if (
            $expertService->expert_id !== $expert->id
        ) {
            throw ValidationException::withMessages([
                'expert_service' => [
                    'Anda tidak memiliki akses ke layanan ini.',
                ],
            ]);
        }
    }
}