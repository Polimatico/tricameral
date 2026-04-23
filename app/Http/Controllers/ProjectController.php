<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectSettingsRequest;
use App\Models\Project;
use App\Models\PullRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();

        $query = Project::accessibleBy($user)
            ->withCount('stars')
            ->with('user');

        if ($search = $request->string('search')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $filter = $request->input('filter', 'all');

        match ($filter) {
            'mine' => $query->where('user_id', $user->id),
            'invited' => $query->where('user_id', '!=', $user->id)
                ->whereHas('members', fn ($m) => $m->where('user_id', $user->id)),
            'other' => $query->where('user_id', '!=', $user->id)
                ->whereDoesntHave('members', fn ($m) => $m->where('user_id', $user->id)),
            default => null,
        };

        $sort = $request->input('sort', 'created');

        match ($sort) {
            'updated' => $query->latest('updated_at'),
            'stars' => $query->orderByDesc('stars_count'),
            default => $query->latest('created_at'),
        };

        $projects = $query->get();

        $starredIds = $user->stars()->pluck('project_id')->flip();

        return view('user.projects.index', [
            'projects' => $projects,
            'starredIds' => $starredIds,
            'filter' => $filter,
            'sort' => $sort,
            'search' => $request->input('search', ''),
        ]);
    }

    public function myProjects(): View
    {
        $projects = Auth::user()
            ->projects()
            ->withCount('stars')
            ->latest()
            ->get();

        return view('user.profile.my_projects', ['projects' => $projects]);
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $project = Auth::user()->projects()->create($request->validated());

        return redirect()->route('projects.show', $project);
    }

    public function show(Project $project): View|RedirectResponse
    {
        if (! $project->canView(Auth::user())) {
            return redirect()->route('projects.index')
                ->with('error', 'Non hai i permessi per visualizzare questa legge.');
        }

        $user = Auth::user();
        $isStarred = $user->stars()->where('project_id', $project->id)->exists();
        $canFork = $project->canFork($user);

        return view('user.projects.show', [
            'project' => $project,
            'isStarred' => $isStarred,
            'canFork' => $canFork,
        ]);
    }

    public function opinions(Project $project): View|RedirectResponse
    {
        $user = Auth::user();

        if (! $project->canView($user)) {
            return redirect()->route('projects.index')
                ->with('error', 'Non hai i permessi per visualizzare questa legge.');
        }

        $opinions = $project->opinions()
            ->with(['user', 'replies.user', 'votes'])
            ->withSum('votes', 'value')
            ->orderByDesc('votes_sum_value')
            ->orderByDesc('created_at')
            ->get();

        $isStarred = $user->stars()->where('project_id', $project->id)->exists();

        return view('user.projects.opinions', [
            'project' => $project,
            'opinions' => $opinions,
            'isStarred' => $isStarred,
        ]);
    }

    public function issues(Project $project): View|RedirectResponse
    {
        if (! $project->canView(Auth::user())) {
            return redirect()->route('projects.index')
                ->with('error', 'Non hai i permessi per visualizzare questa legge.');
        }

        $issues = $project->issues()
            ->with(['user', 'tags'])
            ->withCount('comments')
            ->latest()
            ->get();

        $projectTags = $project->issueTags()->orderBy('label')->get();

        $isStarred = Auth::user()->stars()->where('project_id', $project->id)->exists();

        return view('user.projects.issues', [
            'project' => $project,
            'issues' => $issues,
            'projectTags' => $projectTags,
            'isStarred' => $isStarred,
        ]);
    }

    public function pull(Project $project): View|RedirectResponse
    {
        $user = Auth::user();

        if (! $project->canViewPulls($user)) {
            return redirect()->route('projects.show', $project)
                ->with('error', 'Non hai i permessi per visualizzare le pull request di questa legge.');
        }

        $pullRequests = $project->pullRequests()
            ->with(['user', 'tags'])
            ->withCount('comments')
            ->latest()
            ->get();

        $projectTags = $project->pullRequestTags()->orderBy('label')->get();
        $isStarred = $user->stars()->where('project_id', $project->id)->exists();
        $canCreatePull = $project->canCreatePull($user);

        $myProjects = Project::accessibleBy($user)
            ->where('id', '!=', $project->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('user.projects.pull', [
            'project' => $project,
            'pullRequests' => $pullRequests,
            'projectTags' => $projectTags,
            'isStarred' => $isStarred,
            'canCreatePull' => $canCreatePull,
            'myProjects' => $myProjects,
        ]);
    }

    public function merge(Project $project): View|RedirectResponse
    {
        if ($project->user_id !== Auth::id()) {
            return redirect()->route('projects.show', $project)
                ->with('error', 'Solo il proprietario può accedere alla sezione Merge.');
        }

        $merges = $project->pullRequests()
            ->where('status', 'accepted')
            ->with(['user', 'tags', 'sourceProject'])
            ->withCount('comments')
            ->latest('updated_at')
            ->get();

        $isStarred = Auth::user()->stars()->where('project_id', $project->id)->exists();

        return view('user.projects.merge', [
            'project' => $project,
            'merges' => $merges,
            'isStarred' => $isStarred,
        ]);
    }

    public function mergeShow(Project $project, PullRequest $pullRequest): View|RedirectResponse
    {
        if ($project->user_id !== Auth::id()) {
            return redirect()->route('projects.show', $project)
                ->with('error', 'Solo il proprietario può accedere alla sezione Merge.');
        }

        if (! $pullRequest->isAccepted() || $pullRequest->project_id !== $project->id) {
            return redirect()->route('projects.merge', $project)
                ->with('error', 'Merge non trovato.');
        }

        $pullRequest->load(['user', 'tags', 'sourceProject', 'comments.user']);

        $isStarred = Auth::user()->stars()->where('project_id', $project->id)->exists();

        return view('user.projects.merge_details', [
            'project' => $project,
            'pullRequest' => $pullRequest,
            'isStarred' => $isStarred,
        ]);
    }

    public function team(Project $project): View|RedirectResponse
    {
        $user = Auth::user();

        if (! $project->canView($user)) {
            return redirect()->route('projects.index')
                ->with('error', 'Non hai i permessi per visualizzare questa legge.');
        }

        $members = $project->members()
            ->with('user')
            ->get()
            ->groupBy(fn ($m) => $m->role->value);

        $memberUserIds = $project->members()->pluck('user_id');

        $contributors = $project->pullRequests()
            ->where('status', 'accepted')
            ->with('user')
            ->get()
            ->pluck('user')
            ->unique('id')
            ->reject(fn ($u) => $u->id === $project->user_id || $memberUserIds->contains($u->id))
            ->values();

        $isStarred = $user->stars()->where('project_id', $project->id)->exists();

        return view('user.projects.team', [
            'project' => $project,
            'members' => $members,
            'contributors' => $contributors,
            'isStarred' => $isStarred,
        ]);
    }

    public function settings(Project $project): View|RedirectResponse
    {
        if ($project->user_id !== Auth::id()) {
            return redirect()->route('projects.show', $project)
                ->with('error', 'Solo il proprietario può accedere alle impostazioni.');
        }

        $members = $project->members()->with('user')->get();

        $isStarred = Auth::user()->stars()->where('project_id', $project->id)->exists();

        return view('user.projects.settings', [
            'project' => $project,
            'members' => $members,
            'isStarred' => $isStarred,
        ]);
    }

    public function updateSettings(UpdateProjectSettingsRequest $request, Project $project): RedirectResponse
    {
        $project->update($request->validated());

        return redirect()->route('projects.settings', $project)
            ->with('success', 'Impostazioni salvate.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        if ($project->user_id !== Auth::id()) {
            return redirect()->route('projects.show', $project)
                ->with('error', 'Solo il proprietario può eliminare questa legge.');
        }

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Legge eliminata.');
    }
}
