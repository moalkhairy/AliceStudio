<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientOrder extends Model
{
    protected $fillable = [
        'client_id', 'coin_package_id', 'currency', 'price', 'discount_percent', 'final_price', 'status', 'payment_ref'
    ];

    public function package()
    {
        return $this->belongsTo(CoinPackage::class, 'coin_package_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
