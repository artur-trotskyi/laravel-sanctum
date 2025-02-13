<?php

use App\Http\Controllers\Auth\PersonalAccessTokenController;
use App\Http\Controllers\Auth\SanctumAuthController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:auth'])->prefix('auth')->as('auth.')->group(function () {
    Route::post('register', [SanctumAuthController::class, 'register'])->name('register');
    Route::post('login', [SanctumAuthController::class, 'login'])->name('login');

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('logout', [SanctumAuthController::class, 'logout'])->name('logout');
        Route::post('me', [SanctumAuthController::class, 'me'])->name('me');
        Route::delete('/personal-access-tokens', [PersonalAccessTokenController::class, 'destroy']);
    });

    Route::post('/personal-access-tokens', [PersonalAccessTokenController::class, 'store']);
});
