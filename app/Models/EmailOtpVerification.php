<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailOtpVerification extends Model
{
    protected $fillable = ['client_id', 'code_hash', 'expires_at', 'attempts', 'max_attempts', 'status', 'last_sent_at'];
    protected $casts = ['expires_at' => 'datetime', 'last_sent_at' => 'datetime'];
}
