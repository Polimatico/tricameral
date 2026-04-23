<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\PullRequest;
use App\Models\PullRequestComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PullRequestCommentController extends Controller
{
    public function store(Request $request, Project $project, PullRequest $pullRequest): RedirectResponse
    {
        if (! $project->canViewPulls(Auth::user())) {
            return redirect()->route('projects.index')
                ->with('error', 'Non hai i permessi per accedere a questa legge.');
        }

        $validated = $request->validate([
            'body' => ['required', 'string'],
        ]);

        $pullRequest->comments()->create([
            'user_id' => Auth::id(),
            'body' => $validated['body'],
        ]);

        return redirect()->route('projects.pull.show', [$project, $pullRequest])
            ->with('success', 'Commento aggiunto.');
    }

    public function destroy(Project $project, PullRequest $pullRequest, PullRequestComment $comment): RedirectResponse
    {
        $isOwn = $comment->user_id === Auth::id();
        $isAdmin = $project->isAdminFor(Auth::user());

        if (! $isOwn && ! $isAdmin) {
            return redirect()->route('projects.pull.show', [$project, $pullRequest])
                ->with('error', 'Non puoi eliminare questo commento.');
        }

        $comment->delete();

        return redirect()->route('projects.pull.show', [$project, $pullRequest])
            ->with('success', 'Commento eliminato.');
    }
}
