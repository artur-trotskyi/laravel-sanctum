<?php

use App\Enums\Ability\AbilityEnum;
use App\Http\Controllers\API\V1\TicketController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:sanctum',
    'abilities:'. AbilityEnum::MANAGE_ENTITIES_FULL_ACCESS->value,
    'throttle:api'
])->prefix('v1')->group(function () {
    Route::apiResource('tickets', TicketController::class);
});
