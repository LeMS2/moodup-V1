<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubTrigger extends Model
{
    public function suggestions()
    {
        return $this->hasMany(Suggestion::class);
    }
    
    public function resources()
    {
        return $this->belongsToMany(Resource::class, 'sub_trigger_resource');
    }
}
