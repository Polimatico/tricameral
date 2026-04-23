<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\IssueComment;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IssueCommentController extends Controller
{
    public function store(Request $request, Project $project, Issue $issue): RedirectResponse
    {
        if (! $project->canView(Auth::user())) {
            return redirect()->route('projects.index')
                ->with('error', 'Non hai i permessi per accedere a questa legge.');
        }

        $validated = $request->validate([
            'body' => ['required', 'string'],
        ]);

        $issue->comments()->create([
            'user_id' => Auth::id(),
            'body' => $validated['body'],
        ]);

        return redirect()->route('projects.issues.show', [$project, $issue])
            ->with('success', 'Commento aggiunto.');
    }

    public function destroy(Project $project, Issue $issue, IssueComment $comment): RedirectResponse
    {
        $isOwn = $comment->user_id === Auth::id();
        $isAdmin = $project->isAdminFor(Auth::user());

        if (! $isOwn && ! $isAdmin) {
            return redirect()->route('projects.issues.show', [$project, $issue])
                ->with('error', 'Non puoi eliminare questo commento.');
        }

        $comment->delete();

        return redirect()->route('projects.issues.show', [$project, $issue])
            ->with('success', 'Commento eliminato.');
    }
}
