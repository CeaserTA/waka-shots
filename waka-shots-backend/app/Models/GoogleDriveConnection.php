<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleDriveConnection extends Model
{
    protected $fillable = [
        'access_token',
        'refresh_token',
        'token_expires_at',
        'connected_at',
        'connected_email',
    ];

    protected function casts(): array
    {
        return [
            'access_token' => 'encrypted',
            'refresh_token' => 'encrypted',
            'token_expires_at' => 'datetime',
            'connected_at' => 'datetime',
        ];
    }
}
