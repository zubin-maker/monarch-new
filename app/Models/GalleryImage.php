<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GalleryImage extends Model
{
    use HasFactory;


    protected $table = 'gallery_images';
    
     protected $fillable = ['item_id', 'image'];
   

    public function item()
    {
       return $this->belongsTo(User\UserItem::class, 'item_id')->withDefault([
        'title' => 'Item not found',
    ]);
    }

    /**
     * If you have item contents related by item_id → id (of items)
     */
    public function itemContents()
    {
        return $this->hasMany(User\UserItemContent::class, 'item_id', 'item_id');
    }
}