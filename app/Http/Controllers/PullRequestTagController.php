<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\PullRequest;
use App\Models\PullRequestTag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PullRequestTagController extends Controller
{
    public function store(Request $request, Project $project): RedirectResponse
    {
        if (! $project->isAdminFor(Auth::user())) {
            return redirect()->route('projects.pull', $project)
                ->with('error', 'Solo gli admin possono creare tag per le pull request.');
        }

        $validated = $request->validate([
            'label' => ['required', 'string', 'max:50'],
            'color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $project->pullRequestTags()->create($validated);

        return redirect()->route('projects.pull', $project)
            ->with('success', 'Tag creato.');
    }

    public function destroy(Project $project, PullRequestTag $tag): RedirectResponse
    {
        if (! $project->isAdminFor(Auth::user())) {
            return redirect()->route('projects.pull', $project)
                ->with('error', 'Solo gli admin possono eliminare i tag.');
        }

        $tag->delete();

        return redirect()->route('projects.pull', $project)
            ->with('success', 'Tag eliminato.');
    }

    public function attach(Request $request, Project $project, PullRequest $pullRequest): RedirectResponse
    {
        if (! $project->isAdminFor(Auth::user())) {
            return redirect()->route('projects.pull.show', [$project, $pullRequest])
                ->with('error', 'Solo gli admin possono assegnare tag.');
        }

        $validated = $request->validate([
            'tag_id' => ['required', 'integer', 'exists:pull_request_tags,id'],
        ]);

        $pullRequest->tags()->syncWithoutDetaching([$validated['tag_id']]);

        return redirect()->route('projects.pull.show', [$project, $pullRequest]);
    }

    public function detach(Project $project, PullRequest $pullRequest, PullRequestTag $tag): RedirectResponse
    {
        if (! $project->isAdminFor(Auth::user())) {
            return redirect()->route('projects.pull.show', [$project, $pullRequest])
                ->with('error', 'Solo gli admin possono rimuovere tag.');
        }

        $pullRequest->tags()->detach($tag->id);

        return redirect()->route('projects.pull.show', [$project, $pullRequest]);
    }
}
