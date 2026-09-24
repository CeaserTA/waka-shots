<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Gallery extends Model
{
    protected $fillable = [
        'client_name',
        'client_email',
        'event_name',
        'event_date',
        'drive_folder_id',
        'access_token',
        'password_hash',
        'expires_at',
        'is_active',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
            'password_hash' => 'hashed',
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

    /**
     * Every gallery gets an access code on creation; only galleries that
     * predate the feature have none, and stay open until an admin issues one.
     */
    public function hasPassword(): bool
    {
        return filled($this->password_hash);
    }

    public function verifyPassword(string $plain): bool
    {
        return $this->hasPassword() && Hash::check($plain, $this->password_hash);
    }

    /**
     * Assigns a fresh 6-digit code (hashed by the cast) and returns the
     * plaintext, which is unrecoverable once this call returns. Does not save.
     */
    public function setNewAccessCode(): string
    {
        $code = (string) random_int(100000, 999999);

        $this->password_hash = $code;

        return $code;
    }
}
