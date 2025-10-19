<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Studio extends Model
{
    protected $fillable = ['code', 'name', 'description', 'order', 'is_active'];

    public function steps()
    {
        return $this->hasMany(Step::class)->orderBy('order');
    }

    public function groups()
    {
        return $this->hasMany(Group::class)->orderBy('order');
    }

    public function sections()
    {
        return $this->hasMany(Section::class)->orderBy('order');
    }
}
