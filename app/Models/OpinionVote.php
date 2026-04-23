<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpinionVote extends Model
{
    protected $fillable = ['opinion_id', 'user_id', 'value'];

    public function opinion(): BelongsTo
    {
        return $this->belongsTo(Opinion::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
