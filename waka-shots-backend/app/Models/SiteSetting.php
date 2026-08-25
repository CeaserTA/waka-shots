<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'studio_name',
        'contact_email',
        'contact_phone',
        'whatsapp_number',
        'address',
        'instagram_url',
        'youtube_url',
        'facebook_url',
        'hero_tagline',
    ];
}
