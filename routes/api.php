<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExpertController;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return response()->json([
        'message' => 'HelpIO Backend API OK',
    ]);
});

Route::apiResource('categories', CategoryController::class);

Route::apiResource('experts', ExpertController::class);