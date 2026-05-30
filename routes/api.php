<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MoodController;
use App\Http\Controllers\Api\MoodSummaryController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ResourceController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\TriggerController;
use App\Http\Controllers\Api\SubTriggerController;
use App\Http\Controllers\Api\LogController; // ✅ NOVO IMPORT
use App\Http\Controllers\Api\AdminController; // 🔥 ADICIONE ESTA LINHA

Route::get('/health', fn () => response()->json([
    'status' => 'ok',
    'app' => 'MoodUp API',
    'time' => now()->toIso8601String()
]));

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/auth/accept-terms', [AuthController::class, 'acceptTerms']);
    Route::post('/account/deactivate', [AuthController::class, 'deactivateAccount']);

    // =========================
    // 📌 ROTAS DE MOODS
    // =========================
    Route::apiResource('moods', MoodController::class);

    // =========================
    // 📌 SUMMARY E INSIGHTS
    // =========================
    Route::get('/moods/summary', [MoodController::class, 'summary']);
    Route::get('/moods/summary/weekly', [MoodSummaryController::class, 'weekly']);
    Route::get('/moods/summary/monthly', [MoodSummaryController::class, 'monthly']);
    Route::get('/moods/insights/weekly', [MoodSummaryController::class, 'weeklyInsights']);

    // =========================
    // 📌 ESTATÍSTICAS
    // =========================
    Route::get('/moods/stats/daily', [MoodController::class, 'dailyStats']);
    Route::get('/moods/stats/monthly', [MoodController::class, 'monthlyStats']);

    // =========================
    // 📊 RELATÓRIOS
    // =========================
    Route::get('/stats/top-triggers', [MoodSummaryController::class, 'topTriggers']);
    Route::get('/stats/top-resources', [MoodSummaryController::class, 'topResources']);
    Route::get('/stats/overview', [MoodSummaryController::class, 'statsOverview']);

    // =========================
    // 📌 CATEGORIAS
    // =========================
    Route::apiResource('categories', CategoryController::class);

    // =========================
    // 🤖 AI E RECURSOS
    // =========================
    Route::post('/ai/chat', [\App\Http\Controllers\Api\AIChatController::class, 'chat']);

    Route::get('/resources', [ResourceController::class, 'index']);
    Route::get('/resources/recommendation', [ResourceController::class, 'recommend']);
    Route::get('/resources/recommendation/history', [ResourceController::class, 'recommendByHistory']);

    // =========================
    // 💬 FEEDBACK
    // =========================
    Route::post('/feedback', [FeedbackController::class, 'store']);

    // =========================
    // 🔥 TRIGGERS
    // =========================
    Route::get('/triggers', [TriggerController::class, 'index']);
    Route::get('/triggers/{id}', [TriggerController::class, 'show']);
    Route::post('/triggers', [TriggerController::class, 'store']);
    Route::put('/triggers/{id}', [TriggerController::class, 'update']);
    Route::delete('/triggers/{id}', [TriggerController::class, 'destroy']);

    // =========================
    // 🔥 SUB-TRIGGERS
    // =========================
    Route::get('/sub-triggers', [SubTriggerController::class, 'index']);
    Route::get('/sub-triggers/{id}', [SubTriggerController::class, 'show']);
    Route::post('/sub-triggers', [SubTriggerController::class, 'store']);
    Route::put('/sub-triggers/{id}', [SubTriggerController::class, 'update']);
    Route::delete('/sub-triggers/{id}', [SubTriggerController::class, 'destroy']);

    // =========================
    // 🔐 ROTAS ADMIN (protegidas)
    // =========================
    Route::middleware(['admin'])->group(function () {
        Route::get('/admin/activity-logs', [AdminController::class, 'getActivityLogs']);
        Route::get('/admin/users', [AdminController::class, 'getUsers']);
        Route::put('/admin/users/{id}/role', [AdminController::class, 'updateUserRole']);
    });
});