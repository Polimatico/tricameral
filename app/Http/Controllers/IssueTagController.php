<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\IssueTag;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IssueTagController extends Controller
{
    public function store(Request $request, Project $project): RedirectResponse
    {
        if (! $project->isAdminFor(Auth::user())) {
            return redirect()->route('projects.issues', $project)
                ->with('error', 'Solo gli admin possono creare tag.');
        }

        $validated = $request->validate([
            'label' => ['required', 'string', 'max:50'],
            'color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        $project->issueTags()->create($validated);

        return back()->with('success', 'Tag creato.');
    }

    public function destroy(Project $project, IssueTag $tag): RedirectResponse
    {
        if (! $project->isAdminFor(Auth::user())) {
            return redirect()->route('projects.issues', $project)
                ->with('error', 'Solo gli admin possono eliminare tag.');
        }

        if ($tag->project_id !== $project->id) {
            return back()->with('error', 'Tag non valido.');
        }

        $tag->delete();

        return back()->with('success', 'Tag eliminato.');
    }

    public function attach(Request $request, Project $project, Issue $issue): RedirectResponse
    {
        if (! $project->isAdminFor(Auth::user())) {
            return redirect()->route('projects.issues.show', [$project, $issue])
                ->with('error', 'Solo gli admin possono assegnare tag.');
        }

        $validated = $request->validate([
            'tag_id' => ['required', 'integer', 'exists:issue_tags,id'],
        ]);

        $tag = IssueTag::find($validated['tag_id']);

        if ($tag->project_id !== $project->id) {
            return back()->with('error', 'Tag non valido.');
        }

        $issue->tags()->syncWithoutDetaching([$validated['tag_id']]);

        return back()->with('success', 'Tag assegnato.');
    }

    public function detach(Project $project, Issue $issue, IssueTag $tag): RedirectResponse
    {
        if (! $project->isAdminFor(Auth::user())) {
            return redirect()->route('projects.issues.show', [$project, $issue])
                ->with('error', 'Solo gli admin possono rimuovere tag.');
        }

        $issue->tags()->detach($tag->id);

        return back()->with('success', 'Tag rimosso.');
    }
}
