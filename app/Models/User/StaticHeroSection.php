<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaticHeroSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'language_id',
        'title',
        'subtitle',
        'button_text',
        'button_url',
        'background_image',
        'hero_image',
    ];
}
