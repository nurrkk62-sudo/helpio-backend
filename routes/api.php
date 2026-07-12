<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExpertController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Test Route
|--------------------------------------------------------------------------
*/

Route::get('/test', function () {
    return response()->json([
        'message' => 'HelpIO Backend API OK',
    ]);
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    Route::post(
        '/register-user',
        [AuthController::class, 'register']
    );

    Route::post(
        '/login',
        [AuthController::class, 'login']
    );

    Route::middleware('auth:sanctum')->get(
        '/me',
        function (Request $request) {
            return response()->json([
                'status' => 'success',
                'data' => $request->user(),
                'message' => 'Data user berhasil diambil.',
            ], 200);
        }
    );
});

/*
|--------------------------------------------------------------------------
| Protected API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Category Routes
    |--------------------------------------------------------------------------
    */

    Route::apiResource(
        'categories',
        CategoryController::class
    )->except([
        'destroy',
    ]);

    Route::delete(
        '/categories/{category}',
        [CategoryController::class, 'destroy']
    )->middleware('role:admin');

    /*
    |--------------------------------------------------------------------------
    | Expert Routes
    |--------------------------------------------------------------------------
    */

    Route::apiResource(
        'experts',
        ExpertController::class
    )->except([
        'destroy',
    ]);

    Route::delete(
        '/experts/{expert}',
        [ExpertController::class, 'destroy']
    )->middleware('role:admin');
});