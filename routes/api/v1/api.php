<?php

use App\Enums\Auth\TokenAbilityEnum;
use App\Http\Controllers\API\V1\TicketController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:sanctum',
    'abilities:'.TokenAbilityEnum::ACCESS_API->value,
    'throttle:api',
])->prefix('v1')->group(function () {
    Route::apiResource('tickets', TicketController::class);
});
