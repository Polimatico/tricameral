<?php

namespace App\Models;

use App\Enums\ForkListing;
use App\Enums\ForkPermission;
use App\Enums\ProjectRole;
use App\Enums\ProjectVisibility;
use App\Enums\PullPermission;
use App\Enums\PullVisibility;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'readme',
        'conduct_code',
        'law_text',
        'visibility',
        'fork_permission',
        'forked_from_id',
        'fork_listing',
        'fork_visible',
        'pull_permission',
        'pull_visibility',
    ];

    protected function casts(): array
    {
        return [
            'visibility' => ProjectVisibility::class,
            'fork_permission' => ForkPermission::class,
            'fork_listing' => ForkListing::class,
            'fork_visible' => 'boolean',
            'pull_permission' => PullPermission::class,
            'pull_visibility' => PullVisibility::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function opinions(): HasMany
    {
        return $this->hasMany(Opinion::class);
    }

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class);
    }

    public function issueTags(): HasMany
    {
        return $this->hasMany(IssueTag::class);
    }

    public function stars(): HasMany
    {
        return $this->hasMany(Star::class);
    }

    public function starredByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'stars')->withTimestamps();
    }

    public function forkedFrom(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'forked_from_id');
    }

    public function forks(): HasMany
    {
        return $this->hasMany(Project::class, 'forked_from_id');
    }

    public function pullRequests(): HasMany
    {
        return $this->hasMany(PullRequest::class);
    }

    public function pullRequestTags(): HasMany
    {
        return $this->hasMany(PullRequestTag::class);
    }

    public function canView(User $user): bool
    {
        return match ($this->visibility) {
            ProjectVisibility::Public => true,
            ProjectVisibility::Private => $this->user_id === $user->id,
            ProjectVisibility::Restricted => $this->user_id === $user->id
                || $this->members()->where('user_id', $user->id)->exists(),
        };
    }

    public function isAdminFor(User $user): bool
    {
        if ($this->user_id === $user->id) {
            return true;
        }

        return $this->members()
            ->where('user_id', $user->id)
            ->where('role', ProjectRole::Admin->value)
            ->exists();
    }

    public function canFork(User $user): bool
    {
        if ($this->fork_permission === ForkPermission::Disabled) {
            return false;
        }

        if (! $this->canView($user)) {
            return false;
        }

        if ($this->fork_permission === ForkPermission::Everyone) {
            return true;
        }

        if ($this->user_id === $user->id) {
            return true;
        }

        $member = $this->members()->where('user_id', $user->id)->first();

        if (! $member) {
            return false;
        }

        return match ($this->fork_permission) {
            ForkPermission::MembersOnly => true,
            ForkPermission::EditorsOnly => in_array($member->role, [ProjectRole::Editor, ProjectRole::Admin]),
            ForkPermission::AdminsOnly => $member->role === ProjectRole::Admin,
            default => false,
        };
    }

    public function canViewPulls(User $user): bool
    {
        if (! $this->canView($user)) {
            return false;
        }

        return match ($this->pull_visibility) {
            PullVisibility::Everyone => true,
            PullVisibility::MembersOnly => $this->user_id === $user->id
                || $this->members()->where('user_id', $user->id)->exists(),
            PullVisibility::AdminsOnly => $this->isAdminFor($user),
        };
    }

    public function canCreatePull(User $user): bool
    {
        if ($this->pull_permission === PullPermission::Disabled) {
            return false;
        }

        if (! $this->canView($user)) {
            return false;
        }

        if ($this->pull_permission === PullPermission::Everyone) {
            return true;
        }

        if ($this->user_id === $user->id) {
            return true;
        }

        $member = $this->members()->where('user_id', $user->id)->first();

        if (! $member) {
            return false;
        }

        return match ($this->pull_permission) {
            PullPermission::MembersOnly => true,
            PullPermission::EditorsOnly => in_array($member->role, [ProjectRole::Editor, ProjectRole::Admin]),
            PullPermission::AdminsOnly => $member->role === ProjectRole::Admin,
            default => false,
        };
    }

    /** @param Builder<Project> $query */
    public function scopeAccessibleBy(Builder $query, User $user): void
    {
        $query->where(function (Builder $q) use ($user) {
            $q->where('visibility', ProjectVisibility::Public->value)
                ->orWhere('user_id', $user->id)
                ->orWhereHas('members', fn (Builder $m) => $m->where('user_id', $user->id));
        });
    }
}
