<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryAccessLog extends Model
{
    protected $fillable = [
        'gallery_id',
        'event_type',
        'image_id',
        'ip_address',
        'user_agent',
    ];

    public function gallery(): BelongsTo
    {
        return $this->belongsTo(Gallery::class);
    }
}
