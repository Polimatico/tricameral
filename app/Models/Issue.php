<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Issue extends Model
{
    protected $fillable = [
        'project_id',
        'user_id',
        'title',
        'body',
        'status',
    ];

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(IssueComment::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(IssueTag::class, 'issue_tag', 'issue_id', 'issue_tag_id');
    }
}
