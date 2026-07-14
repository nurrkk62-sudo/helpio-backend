<?php

namespace App\Services;

use App\Models\ExpertVerification;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ExpertVerificationService
{
    public function submit(
        User $user,
        array $data
    ): ExpertVerification {
        if ($user->role !== 'expert') {
            throw ValidationException::withMessages([
                'expert' => [
                    'Hanya expert yang dapat mengajukan verifikasi.',
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

        $verification = ExpertVerification::query()
            ->where('expert_id', $expert->id)
            ->first();

        if (
            $verification &&
            $verification->status === 'approved'
        ) {
            throw ValidationException::withMessages([
                'verification' => [
                    'Expert sudah terverifikasi.',
                ],
            ]);
        }

        $verification = ExpertVerification::updateOrCreate(
            [
                'expert_id' => $expert->id,
            ],
            [
                'identity_number' =>
                    $data['identity_number'],

                'identity_document' =>
                    $data['identity_document'],

                'certificate_document' =>
                    $data['certificate_document'] ?? null,

                'notes' =>
                    $data['notes'] ?? null,

                'status' => 'pending',
                'admin_notes' => null,
                'reviewed_by' => null,
                'reviewed_at' => null,
            ]
        );

        $expert->update([
            'verified' => false,
            'verification_status' => 'pending',
        ]);

        return $verification->load([
            'expert.user',
            'expert.category',
        ]);
    }

    public function getMyVerification(
        User $user
    ): ExpertVerification {
        if ($user->role !== 'expert') {
            throw ValidationException::withMessages([
                'expert' => [
                    'Hanya expert yang dapat melihat data verifikasi.',
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

        $verification = ExpertVerification::query()
            ->where('expert_id', $expert->id)
            ->with([
                'expert.user',
                'expert.category',
                'reviewer',
            ])
            ->first();

        if (! $verification) {
            throw ValidationException::withMessages([
                'verification' => [
                    'Pengajuan verifikasi belum ditemukan.',
                ],
            ]);
        }

        return $verification;
    }

    public function pending(): Collection
    {
        return ExpertVerification::query()
            ->where('status', 'pending')
            ->with([
                'expert.user',
                'expert.category',
            ])
            ->latest()
            ->get();
    }

    public function review(
        User $admin,
        ExpertVerification $verification,
        array $data
    ): ExpertVerification {
        if ($admin->role !== 'admin') {
            throw ValidationException::withMessages([
                'admin' => [
                    'Hanya admin yang dapat memproses verifikasi expert.',
                ],
            ]);
        }

        if ($verification->status !== 'pending') {
            throw ValidationException::withMessages([
                'verification' => [
                    'Pengajuan verifikasi ini sudah diproses.',
                ],
            ]);
        }

        return DB::transaction(function () use (
            $admin,
            $verification,
            $data
        ): ExpertVerification {
            $status = $data['status'];

            $verification->update([
                'status' => $status,
                'admin_notes' =>
                    $data['admin_notes'] ?? null,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ]);

            $verification->expert()->update([
                'verified' => $status === 'approved',
                'verification_status' => $status,
            ]);

            return $verification->fresh([
                'expert.user',
                'expert.category',
                'reviewer',
            ]);
        });
    }
}