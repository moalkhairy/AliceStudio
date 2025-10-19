<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StepSection extends Model
{
    protected $fillable = [
        'studio_id', 'step_id', 'section_id', 'order', 'is_active'
    ];

    public function studio()
    {
        return $this->belongsTo(Studio::class);
    }

    public function step()
    {
        return $this->belongsTo(Step::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
