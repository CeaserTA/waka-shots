<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Gallery extends Model
{
    protected $fillable = [
        'client_name',
        'event_name',
        'event_date',
        'drive_folder_id',
        'access_token',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Gallery $gallery): void {
            if (filled($gallery->access_token)) {
                return;
            }

            do {
                $token = Str::random(32);
            } while (static::where('access_token', $token)->exists());

            $gallery->access_token = $token;
        });
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(GalleryAccessLog::class);
    }
}
