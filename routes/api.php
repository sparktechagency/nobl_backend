<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\Backend\CategoryController;
use App\Http\Controllers\api\Backend\DashboardController;
use App\Http\Controllers\api\Backend\DocumentController;
use App\Http\Controllers\api\Backend\LinkController;
use App\Http\Controllers\api\Backend\PageController;
use App\Http\Controllers\api\Backend\PhotoLibraryController;
use App\Http\Controllers\api\Backend\UserController;
use App\Http\Controllers\api\Backend\VideoController;
use App\Http\Controllers\api\Frontend\HomeController;
use App\Http\Controllers\api\Frontend\VideoCommentController;
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
    Route::get('get-category', [CategoryController::class, 'getCategory']);
    Route::get('dashboard', DashboardController::class);
    Route::resource('pages', PageController::class)->only(['index', 'store']);
    Route::resource('users', UserController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('photos', PhotoLibraryController::class);
    Route::resource('videos', VideoController::class);
    Route::resource('documents', DocumentController::class);
    Route::resource('links', LinkController::class);
});

// user routes
Route::middleware(['auth:sanctum', 'user'])->group(function () {
    Route::resource('pages', PageController::class)->only('index');
    Route::get('home', HomeController::class);
    Route::get('get-category', [CategoryController::class, 'getCategory']);
    Route::resource('videos', VideoController::class)->only('index');
    Route::resource('photos', PhotoLibraryController::class)->only('index');
    Route::resource('documents', DocumentController::class)->only('index');
    Route::resource('links', LinkController::class)->only('index');

    Route::get('video-comment', [VideoCommentController::class, 'getComment']);
    Route::post('video-comment', [VideoCommentController::class, 'storeComment']);
    Route::get('related-videos', [VideoController::class, 'relatedVideos']);
});

// app open count
Route::get('app-open-count', [AuthController::class, 'appOpenCount']);
