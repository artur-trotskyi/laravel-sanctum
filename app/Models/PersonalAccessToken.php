<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumToken;

class PersonalAccessToken extends SanctumToken
{
    protected $table = 'personal_access_tokens';

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'token',
        'abilities',
        'expires_at',
        'ip',
        'user_agent',
    ];

    public function __construct(array $attributes = [])
    {
        $attributes['ip'] = request()->ip();
        $attributes['user_agent'] = request()->userAgent();

        parent::__construct($attributes);
    }
}
