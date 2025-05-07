<?php

namespace App\Models;

use App\Enums\ApiProvider;
use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'provider',
        'url',
        'request',
        'response',
        'exception',
    ];

    protected $casts = [
        'provider' => ApiProvider::class,
        'request' => 'json',
        'response' => 'json',
    ];
}
