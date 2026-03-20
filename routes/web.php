<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => 'MoodUp API',
        'status' => 'ok',
        'docs' => '/docs',
        'time' => now()->toIso8601String(),
    ]);
});
