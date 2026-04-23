<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class IssueController extends Controller
{
    public function store(Request $request, Project $project): RedirectResponse
    {
        if (! $project->canView(Auth::user())) {
            return redirect()->route('projects.index')
                ->with('error', 'Non hai i permessi per accedere a questa legge.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        $issue = $project->issues()->create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'body' => $validated['body'],
        ]);

        return redirect()->route('projects.issues.show', [$project, $issue]);
    }

    public function show(Project $project, Issue $issue): View|RedirectResponse
    {
        if (! $project->canView(Auth::user())) {
            return redirect()->route('projects.index')
                ->with('error', 'Non hai i permessi per accedere a questa legge.');
        }

        $issue->load(['user', 'comments.user', 'tags']);
        $projectTags = $project->issueTags()->orderBy('label')->get();

        return view('user.projects.issue_details', [
            'project' => $project,
            'issue' => $issue,
            'projectTags' => $projectTags,
        ]);
    }

    public function updateStatus(Project $project, Issue $issue): RedirectResponse
    {
        if (! $project->isAdminFor(Auth::user())) {
            return redirect()->route('projects.issues.show', [$project, $issue])
                ->with('error', 'Solo gli admin possono modificare lo stato delle issue.');
        }

        $issue->update(['status' => $issue->isOpen() ? 'closed' : 'open']);

        return back()->with('success', $issue->isOpen() ? 'Issue riaperta.' : 'Issue chiusa.');
    }

    public function destroy(Project $project, Issue $issue): RedirectResponse
    {
        if (! $project->isAdminFor(Auth::user())) {
            return redirect()->route('projects.issues', $project)
                ->with('error', 'Solo gli admin possono eliminare le issue.');
        }

        $issue->delete();

        return redirect()->route('projects.issues', $project)
            ->with('success', 'Issue eliminata.');
    }
}
