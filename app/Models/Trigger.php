<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Mood;

class Trigger extends Model
{
    protected $fillable = [
        'name',
        'label',
    ];

    public function moods()
    {
        return $this->belongsToMany(Mood::class);
    }

    public function subTriggers()
    {
        return $this->hasMany(SubTrigger::class, 'trigger', 'name');
    }
}
