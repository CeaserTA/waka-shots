<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'tagline', 'description', 'has_packages', 'thumbnail_path', 'amount', 'sort_order'];

    protected function casts(): array
    {
        return [
            'has_packages' => 'boolean',
            'amount' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Service $service): void {
            if ($service->has_packages) {
                $service->amount = null;
            }
        });
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    public function enquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class);
    }
}
