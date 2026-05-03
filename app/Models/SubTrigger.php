<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubTrigger extends Model
{
    public function suggestions()
    {
        return $this->hasMany(Suggestion::class);
    }
}
