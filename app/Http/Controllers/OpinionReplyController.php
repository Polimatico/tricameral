<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOpinionReplyRequest;
use App\Http\Requests\UpdateOpinionReplyRequest;
use App\Models\Opinion;
use App\Models\OpinionReply;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class OpinionReplyController extends Controller
{
    public function store(StoreOpinionReplyRequest $request, Project $project, Opinion $opinion): RedirectResponse
    {
        abort_unless($opinion->project_id === $project->id, 404);
        abort_unless($project->canView(Auth::user()), 403);

        $opinion->replies()->create([
            'user_id' => Auth::id(),
            'body' => $request->validated()['body'],
        ]);

        return redirect()->route('projects.opinions.show', [$project, $opinion]);
    }

    public function update(UpdateOpinionReplyRequest $request, Project $project, Opinion $opinion, OpinionReply $reply): RedirectResponse
    {
        abort_unless($opinion->project_id === $project->id, 404);
        abort_unless($reply->opinion_id === $opinion->id, 404);
        abort_unless($reply->user_id === Auth::id(), 403);

        $reply->update(['body' => $request->validated()['body']]);

        return redirect()->route('projects.opinions.show', [$project, $opinion]);
    }

    public function destroy(Project $project, Opinion $opinion, OpinionReply $reply): RedirectResponse
    {
        abort_unless($opinion->project_id === $project->id, 404);
        abort_unless($reply->opinion_id === $opinion->id, 404);
        abort_unless(
            $reply->user_id === Auth::id() || $project->isAdminFor(Auth::user()),
            403
        );

        $reply->delete();

        return redirect()->route('projects.opinions.show', [$project, $opinion]);
    }
}
