<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\PullRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PullRequestController extends Controller
{
    public function store(Request $request, Project $project): RedirectResponse
    {
        $user = Auth::user();

        if (! $project->canCreatePull($user)) {
            return redirect()->route('projects.pull', $project)
                ->with('error', 'Non hai i permessi per aprire una pull request su questa legge.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'source_project_id' => ['nullable', 'integer', 'exists:projects,id'],
        ]);

        $pullRequest = $project->pullRequests()->create([
            'user_id' => $user->id,
            'title' => $validated['title'],
            'body' => $validated['body'],
            'source_project_id' => $validated['source_project_id'] ?? null,
        ]);

        return redirect()->route('projects.pull.show', [$project, $pullRequest]);
    }

    public function show(Project $project, PullRequest $pullRequest): View|RedirectResponse
    {
        $user = Auth::user();

        if (! $project->canViewPulls($user)) {
            return redirect()->route('projects.index')
                ->with('error', 'Non hai i permessi per visualizzare le pull request di questa legge.');
        }

        if ($pullRequest->project_id !== $project->id) {
            return redirect()->route('projects.pull', $project);
        }

        $pullRequest->load(['user', 'comments.user', 'tags', 'sourceProject']);
        $projectTags = $project->pullRequestTags()->orderBy('label')->get();
        $isStarred = $user->stars()->where('project_id', $project->id)->exists();

        return view('user.projects.pull_details', [
            'project' => $project,
            'pullRequest' => $pullRequest,
            'projectTags' => $projectTags,
            'isStarred' => $isStarred,
        ]);
    }

    public function updateStatus(Request $request, Project $project, PullRequest $pullRequest): RedirectResponse
    {
        if (! $project->isAdminFor(Auth::user())) {
            return redirect()->route('projects.pull.show', [$project, $pullRequest])
                ->with('error', 'Solo gli admin possono modificare lo stato delle pull request.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:open,accepted,rejected'],
        ]);

        $pullRequest->update(['status' => $validated['status']]);

        return back()->with('success', match ($validated['status']) {
            'accepted' => 'Pull request accettata.',
            'rejected' => 'Pull request rifiutata.',
            default => 'Pull request riaperta.',
        });
    }

    public function updateBody(Request $request, Project $project, PullRequest $pullRequest): RedirectResponse
    {
        if ($pullRequest->user_id !== Auth::id()) {
            return redirect()->route('projects.pull.show', [$project, $pullRequest])
                ->with('error', 'Solo l\'autore può modificare il testo della pull request.');
        }

        $validated = $request->validate([
            'body' => ['required', 'string'],
        ]);

        $pullRequest->update(['body' => $validated['body']]);

        return redirect()->route('projects.pull.show', [$project, $pullRequest])
            ->with('success', 'Descrizione aggiornata.');
    }

    public function destroy(Project $project, PullRequest $pullRequest): RedirectResponse
    {
        if (! $project->isAdminFor(Auth::user())) {
            return redirect()->route('projects.pull', $project)
                ->with('error', 'Solo gli admin possono eliminare le pull request.');
        }

        $pullRequest->delete();

        return redirect()->route('projects.pull', $project)
            ->with('success', 'Pull request eliminata.');
    }
}
