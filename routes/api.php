<?php
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->group(function () {

});

// admin routes
Route::prefix('admin')->middleware(['auth:sanctum','admin'])->group(function () {

});

// user routes
Route::middleware(['auth:sanctum','user'])->group(function () {

});
