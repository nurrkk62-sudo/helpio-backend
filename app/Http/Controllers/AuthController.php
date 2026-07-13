<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterExpertRequest;
use App\Http\Requests\SendOtpRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Models\User;
use App\Services\AuthService;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected AuthService $authService;

    protected OtpService $otpService;

    public function __construct(
        AuthService $authService,
        OtpService $otpService
    ) {
        $this->authService = $authService;
        $this->otpService = $otpService;
    }

    /**
     * Register user baru.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
            ],

            'phone' => [
                'required',
                'string',
                'max:20',
                'unique:users,phone',
            ],

            'address' => [
                'nullable',
                'string',
            ],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make(
                $validated['password']
            ),
            'phone' => $validated['phone'],
            'role' => 'user',
            'avatar' => null,
            'address' => $validated['address'] ?? null,
        ]);

        $token = $user
            ->createToken('api-token')
            ->plainTextToken;

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
            'message' => 'User berhasil terdaftar.',
        ], 201);
    }

    /**
     * Register expert baru.
     */
    public function registerExpert(
        RegisterExpertRequest $request
    ): JsonResponse {
        $result = $this->authService->registerExpert(
            $request->validated()
        );

        return response()->json([
            'status' => 'success',
            'data' => $result,
            'message' => 'Expert berhasil terdaftar.',
        ], 201);
    }

    /**
     * Mengirim OTP WhatsApp.
     */
    public function sendOtp(
        SendOtpRequest $request
    ): JsonResponse {
        $user = User::where(
            'phone',
            $request->validated('phone')
        )->firstOrFail();

        if ($user->phone_verified_at !== null) {
            return response()->json([
                'status' => 'error',
                'data' => null,
                'message' =>
                    'Nomor WhatsApp sudah diverifikasi.',
            ], 422);
        }

        $code = $this->otpService->generate($user);

        return response()->json([
            'status' => 'success',
            'data' => [
                'phone' => $user->phone,

                /*
                 * Hanya untuk development.
                 * Hapus saat provider WhatsApp aktif.
                 */
                'debug_otp' => $code,
            ],
            'message' => 'Kode OTP berhasil dibuat.',
        ], 200);
    }

    /**
     * Verifikasi OTP WhatsApp.
     */
    public function verifyOtp(
        VerifyOtpRequest $request
    ): JsonResponse {
        $validated = $request->validated();

        $user = User::where(
            'phone',
            $validated['phone']
        )->firstOrFail();

        if ($user->phone_verified_at !== null) {
            return response()->json([
                'status' => 'error',
                'data' => null,
                'message' =>
                    'Nomor WhatsApp sudah diverifikasi.',
            ], 422);
        }

        $this->otpService->verify(
            $user,
            $validated['code']
        );

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user->fresh(),
            ],
            'message' =>
                'Nomor WhatsApp berhasil diverifikasi.',
        ], 200);
    }

    /**
     * Login user.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => [
                'required',
                'email',
            ],

            'password' => [
                'required',
            ],
        ]);

        $user = User::where(
            'email',
            $request->email
        )->first();

        if (
            ! $user ||
            ! Hash::check(
                $request->password,
                $user->password
            )
        ) {
            throw ValidationException::withMessages([
                'email' => [
                    'Email atau password tidak sesuai.',
                ],
            ]);
        }

        $user->tokens()->delete();

        $token = $user
            ->createToken('api-token')
            ->plainTextToken;

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
            'message' => 'User berhasil login.',
        ], 200);
    }
}