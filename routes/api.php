<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\Backend\CategoryController;
use App\Http\Controllers\api\Backend\PageController;
use App\Http\Controllers\api\Backend\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('change-password', [AuthController::class, 'changePassword']);
        Route::post('update-profile', [AuthController::class, 'updateProfile']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);
        Route::get('profile', [AuthController::class, 'profile']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

// admin routes
Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::resource('pages', PageController::class)->only(['index', 'store']);
    Route::resource('users', UserController::class);
    Route::resource('categories', CategoryController::class);
});

// user routes
Route::middleware(['auth:sanctum', 'user'])->group(function () {
    Route::resource('pages', PageController::class)->only('index');
});
