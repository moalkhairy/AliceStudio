<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = [
        'studio_id', 'group_id', 'code', 'name', 'help_text', 'input_type', 'selection_mode',
        'min_select', 'max_select', 'gender_scope', 'is_required', 'is_general',
        'order', 'is_active'
    ];

    public function studio()
    {
        return $this->belongsTo(Studio::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function choices()
    {
        return $this->hasMany(Choice::class)->orderBy('order');
    }
}
