<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Step extends Model
{
    protected $fillable = [
        'studio_id', 'code', 'title', 'subtitle', 'icon', 'tip_text', 'order', 'is_active'
    ];

    public function studio()
    {
        return $this->belongsTo(Studio::class);
    }

    public function stepSections()
    {
        return $this->hasMany(StepSection::class)->orderBy('order');
    }

    public function sections()
    {
        return $this->belongsToMany(Section::class, 'step_sections')
            ->withPivot(['order', 'is_active'])
            ->wherePivot('is_active', 1)
            ->orderBy('step_sections.order');
    }
}
