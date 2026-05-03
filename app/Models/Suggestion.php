<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suggestion extends Model
{
    public function subTrigger()
    {
        return $this->belongsTo(SubTrigger::class);
    }
}
