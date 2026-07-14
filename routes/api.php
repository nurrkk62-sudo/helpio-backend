<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExpertController;
use App\Http\Controllers\ExpertServiceController;
use App\Http\Controllers\ExpertVerificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
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
        '/register-expert',
        [AuthController::class, 'registerExpert']
    );

    Route::post(
        '/send-otp',
        [AuthController::class, 'sendOtp']
    );

    Route::post(
        '/verify-otp',
        [AuthController::class, 'verifyOtp']
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
| Public Expert Service Routes
|--------------------------------------------------------------------------
*/

Route::get(
    '/expert-services',
    [ExpertServiceController::class, 'index']
);

Route::get(
    '/expert-services/{expertService}',
    [ExpertServiceController::class, 'show']
);

Route::get(
    '/experts/{expert}/services',
    [ExpertServiceController::class, 'byExpert']
);

/*
|--------------------------------------------------------------------------
| Public Review Routes
|--------------------------------------------------------------------------
*/

Route::get(
    '/experts/{expert}/reviews',
    [ReviewController::class, 'byExpert']
);

/*
|--------------------------------------------------------------------------
| Protected API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Order Routes
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/orders',
        [OrderController::class, 'store']
    );

    Route::get(
        '/orders/user',
        [OrderController::class, 'userOrders']
    );

    Route::get(
        '/orders/expert',
        [OrderController::class, 'expertOrders']
    );

    Route::patch(
        '/orders/{order}/status',
        [OrderController::class, 'updateStatus']
    );

    /*
    |--------------------------------------------------------------------------
    | Review Routes
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/reviews',
        [ReviewController::class, 'store']
    )->middleware('role:user');

    /*
    |--------------------------------------------------------------------------
    | Expert Verification Routes
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/expert-verifications',
        [ExpertVerificationController::class, 'store']
    )->middleware('role:expert');

    Route::get(
        '/expert-verifications/me',
        [ExpertVerificationController::class, 'me']
    )->middleware('role:expert');

    /*
    |--------------------------------------------------------------------------
    | Admin Expert Verification Routes
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/expert-verifications/pending',
        [ExpertVerificationController::class, 'pending']
    )->middleware('role:admin');

    Route::patch(
        '/admin/expert-verifications/{expertVerification}',
        [ExpertVerificationController::class, 'review']
    )->middleware('role:admin');

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

    Route::middleware([
        'throttle:60,1',
    ])->group(function () {
        Route::apiResource(
            'experts',
            ExpertController::class
        )->except([
            'destroy',
        ]);
    });

    Route::delete(
        '/experts/{expert}',
        [ExpertController::class, 'destroy']
    )->middleware('role:admin');

    /*
    |--------------------------------------------------------------------------
    | Expert Service Protected Routes
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/expert-services',
        [ExpertServiceController::class, 'store']
    )->middleware('role:expert');

    Route::put(
        '/expert-services/{expertService}',
        [ExpertServiceController::class, 'update']
    )->middleware('role:expert');

    Route::patch(
        '/expert-services/{expertService}',
        [ExpertServiceController::class, 'update']
    )->middleware('role:expert');

    Route::delete(
        '/expert-services/{expertService}',
        [ExpertServiceController::class, 'destroy']
    )->middleware('role:expert');
});