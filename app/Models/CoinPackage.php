<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoinPackage extends Model
{
    protected $fillable = [
        'title', 'coins', 'price', 'discount_percent', 'final_price', 'currency', 'is_active', 'sort_order'
    ];
    protected $casts = ['is_active' => 'boolean', 'coins' => 'integer'];
}
