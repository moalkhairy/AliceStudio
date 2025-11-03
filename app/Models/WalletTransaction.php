<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = ['client_id', 'type', 'amount', 'balance_after', 'meta'];
    protected $casts = ['amount' => 'integer', 'balance_after' => 'integer', 'meta' => 'array'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
