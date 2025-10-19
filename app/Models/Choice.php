<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Choice extends Model
{
    protected $fillable = [
        'section_id', 'label', 'slug', 'token', 'negative_token', 'weight', 'icon_url',
        'gender_scope', 'is_default', 'order', 'is_active'
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
