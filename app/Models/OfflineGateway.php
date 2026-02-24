<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OfflineGateway extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'short_description', 'instructions', 'serial_number', 'status', 'is_receipt', 'receipt'];
}
