<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nickname extends Model
{
    protected $fillable = [
        'user_id',
        'nickname',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
