<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Social extends Model
{
    public $table = "user_socials";

    protected $fillable = [
        'icon',
        'background_color',
        'url',
        'serial_number',
        'user_id'
    ];
}
