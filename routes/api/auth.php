<?php

use App\Enums\Ability\AbilityEnum;
use App\Http\Controllers\Auth\SanctumAuthController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:auth'])->prefix('auth')->as('auth.')->group(function () {
    Route::middleware(['guest:sanctum'])->group(function () {
        Route::post('register', [SanctumAuthController::class, 'register'])->name('register');
        Route::post('login', [SanctumAuthController::class, 'login'])->name('login');
    });

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('logout', [SanctumAuthController::class, 'logout'])->name('logout');

        Route::middleware(['abilities:'.AbilityEnum::ISSUE_ACCESS_TOKEN->value])->group(function () {
            Route::post('refresh-token', [SanctumAuthController::class, 'refresh'])->name('refresh');
        });

        Route::middleware(['abilities:'.AbilityEnum::ACCESS_API->value])->group(function () {
            Route::post('me', [SanctumAuthController::class, 'me'])->name('me');
        });
    });
});
