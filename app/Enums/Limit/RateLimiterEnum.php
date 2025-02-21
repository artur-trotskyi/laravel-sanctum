<?php

namespace App\Enums\Limit;

enum RateLimiterEnum: int
{
    // Limits for authenticated users (by user_id)
    case API_USER_PER_MINUTE = 100;

    // Limits for unauthenticated users (by IP)
    case API_IP_PER_MINUTE = 10;
    case AUTH_IP_PER_MINUTE = 20;
}
