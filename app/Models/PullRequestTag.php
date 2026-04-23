<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PullRequestTag extends Model
{
    protected $fillable = [
        'project_id',
        'label',
        'color',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function pullRequests(): BelongsToMany
    {
        return $this->belongsToMany(PullRequest::class, 'pull_request_tag', 'pull_request_tag_id', 'pull_request_id');
    }
}
