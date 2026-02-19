<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MoodController;
use App\Http\Controllers\Api\MoodSummaryController;
use App\Http\Controllers\Api\CategoryController;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('moods', MoodController::class);
    Route::get('/moods/summary/weekly', [MoodSummaryController::class, 'weekly']);
Route::get('/moods/summary/monthly', [MoodSummaryController::class, 'monthly']);
Route::apiResource('categories', CategoryController::class);
});