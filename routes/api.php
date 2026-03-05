<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MoodController;
use App\Http\Controllers\Api\MoodSummaryController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ResourceController;


Route::get('/health', fn () => response()->json([
    'status' => 'ok',
    'app' => 'MoodUp API',
    'time' => now()->toIso8601String()
]));

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    // Route::get('/ping', fn () => response()->json(['ok' => true]));
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('moods', MoodController::class);
    Route::get('/moods/summary/weekly', [MoodSummaryController::class, 'weekly']);
    Route::get('/moods/summary/monthly', [MoodSummaryController::class, 'monthly']);
    Route::apiResource('categories', CategoryController::class);
    Route::post('/ai/chat', [\App\Http\Controllers\Api\AIChatController::class, 'chat']);
    Route::get('/resources', [ResourceController::class, 'index']);
    Route::get('/resources/recommendation', [ResourceController::class, 'recommend']);
    Route::get('/moods/insights/weekly', [MoodSummaryController::class, 'weeklyInsights']);
    Route::get('/resources/recommendation/history', [ResourceController::class, 'recommendByHistory']);
});