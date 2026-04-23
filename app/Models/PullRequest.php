<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PullRequest extends Model
{
    protected $fillable = [
        'project_id',
        'source_project_id',
        'user_id',
        'title',
        'body',
        'status',
    ];

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function sourceProject(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'source_project_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(PullRequestComment::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(PullRequestTag::class, 'pull_request_tag', 'pull_request_id', 'pull_request_tag_id');
    }
}
