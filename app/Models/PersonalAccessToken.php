<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumToken;
use MongoDB\Laravel\Eloquent\DocumentModel;

class PersonalAccessToken extends SanctumToken
{
    use DocumentModel;

    protected $connection = 'mongodb';

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
