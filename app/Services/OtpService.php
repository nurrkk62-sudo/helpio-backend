<?php

namespace App\Services;

use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class OtpService
{
    private const OTP_EXPIRATION_MINUTES = 5;

    private const MAX_ATTEMPTS = 5;

    public function generate(
        User $user,
        string $purpose = 'registration'
    ): string {
        OtpVerification::where(
            'user_id',
            $user->id
        )
            ->where('purpose', $purpose)
            ->whereNull('verified_at')
            ->delete();

        $code = (string) random_int(
            100000,
            999999
        );

        OtpVerification::create([
            'user_id' => $user->id,
            'code' => Hash::make($code),
            'purpose' => $purpose,
            'attempts' => 0,
            'expires_at' => now()->addMinutes(
                self::OTP_EXPIRATION_MINUTES
            ),
        ]);

        return $code;
    }

    public function verify(
        User $user,
        string $code,
        string $purpose = 'registration'
    ): OtpVerification {
        $otp = OtpVerification::where(
            'user_id',
            $user->id
        )
            ->where('purpose', $purpose)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (! $otp) {
            throw ValidationException::withMessages([
                'code' => [
                    'OTP tidak ditemukan.',
                ],
            ]);
        }

        if ($otp->isExpired()) {
            throw ValidationException::withMessages([
                'code' => [
                    'OTP sudah kedaluwarsa.',
                ],
            ]);
        }

        if (
            $otp->attempts >= self::MAX_ATTEMPTS
        ) {
            throw ValidationException::withMessages([
                'code' => [
                    'Batas percobaan OTP telah tercapai.',
                ],
            ]);
        }

        if (! Hash::check($code, $otp->code)) {
            $otp->increment('attempts');

            throw ValidationException::withMessages([
                'code' => [
                    'Kode OTP tidak valid.',
                ],
            ]);
        }

        $otp->update([
            'verified_at' => now(),
        ]);

        $user->update([
            'phone_verified_at' => now(),
        ]);

        return $otp->fresh();
    }
}