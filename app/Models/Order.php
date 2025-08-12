<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'harga', 
        'kuantity', 
        'harga_semua', 
        'status',
    ];
}
