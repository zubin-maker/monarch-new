<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BulkOrder extends Model
{
    use HasFactory;


    protected $table = 'bulk-order';
    
     protected $fillable = ['category_id','item_id', 'phone','email','quantity'];
   

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