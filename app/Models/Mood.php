<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mood extends Model
{
    use HasFactory;

protected $fillable = [
  'user_id',
  'title',
  'date',
  'level',
  'score',
  'note',
  'mood',
  'triggers',
];

protected $casts = [
  'date' => 'date',
  'triggers' => 'array',
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
{
    return $this->belongsToMany(Category::class)->withTimestamps();
}
}