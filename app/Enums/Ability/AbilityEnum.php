<?php

namespace App\Enums\Ability;

enum AbilityEnum: string
{
    case ISSUE_ACCESS_TOKEN = 'issue-access-token';
    case ACCESS_API = 'access-api';
}
