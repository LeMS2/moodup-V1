<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $fillable = [
        'type','title','description','url','author','duration_minutes','tags','is_active'
    ];

    protected $casts = [
        'tags' => 'array',
        'is_active' => 'boolean',
    ];
}