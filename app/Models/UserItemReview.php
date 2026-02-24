<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserItemReview extends Model
{
    use HasFactory;

    protected $table = 'item_reviews';

    protected $fillable = [
        'item_id',
        'name',
        'description',
        'rating',
    ];

    
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}