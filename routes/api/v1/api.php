<?php

use App\Http\Controllers\API\V1\TicketController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'throttle:api'])->prefix('v1')->group(function () {
    Route::apiResource('tickets', TicketController::class);
});
