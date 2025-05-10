<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ContextController;
use App\Http\Controllers\TranslationController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('locales', LocaleController::class);
    Route::apiResource('contexts', ContextController::class);
    Route::apiResource('translations', TranslationController::class);
    Route::get('/translations-search', [TranslationController::class, 'search']);
});

