<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountStatusLog extends Model
{
    protected $fillable = [
    'user_id',
    'acao',
];

public function user()
{
    return $this->belongsTo(User::class);
}
    //
}
