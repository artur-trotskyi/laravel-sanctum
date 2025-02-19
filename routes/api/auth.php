<?php

use App\Http\Controllers\Auth\SanctumAuthController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:auth'])->prefix('auth')->as('auth.')->group(function () {
    Route::post('register', [SanctumAuthController::class, 'register'])->name('register');
    Route::post('login', [SanctumAuthController::class, 'login'])->name('login');
    Route::post('logout', [SanctumAuthController::class, 'logout'])->name('logout');
    Route::post('refresh-token', [SanctumAuthController::class, 'refresh'])->name('refresh');
    Route::post('me', [SanctumAuthController::class, 'me'])->name('me');
});
