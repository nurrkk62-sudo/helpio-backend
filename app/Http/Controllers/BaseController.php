<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    /**
     * Response berhasil.
     */
    protected function successResponse(
        mixed $data,
        string $message = 'Berhasil.',
        int $code = 200
    ): JsonResponse {
        return response()->json([
            'status' => 'success',
            'data' => $data,
            'message' => $message,
        ], $code);
    }

    /**
     * Response gagal.
     */
    protected function errorResponse(
        string $message = 'Terjadi kesalahan.',
        int $code = 400,
        mixed $data = null
    ): JsonResponse {
        return response()->json([
            'status' => 'error',
            'data' => $data,
            'message' => $message,
        ], $code);
    }
}