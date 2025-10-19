<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'studio_id', 'code', 'name', 'description', 'exclusive_sections', 'order', 'is_active'
    ];

    public function studio()
    {
        return $this->belongsTo(Studio::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class)->orderBy('order');
    }
}
