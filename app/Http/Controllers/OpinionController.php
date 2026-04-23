<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOpinionRequest;
use App\Http\Requests\UpdateOpinionRequest;
use App\Models\Opinion;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OpinionController extends Controller
{
    public function show(Project $project, Opinion $opinion): View|RedirectResponse
    {
        if (! $project->canView(Auth::user())) {
            return redirect()->route('projects.index');
        }

        abort_unless($opinion->project_id === $project->id, 404);

        $opinion->load(['user', 'replies.user', 'votes']);

        return view('user.projects.opinions_details', [
            'project' => $project,
            'opinion' => $opinion,
        ]);
    }

    public function store(StoreOpinionRequest $request, Project $project): RedirectResponse
    {
        abort_unless($project->canView(Auth::user()), 403);

        $project->opinions()->create([
            'user_id' => Auth::id(),
            'body' => $request->validated()['body'],
        ]);

        return redirect()->route('projects.opinions', $project);
    }

    public function update(UpdateOpinionRequest $request, Project $project, Opinion $opinion): RedirectResponse
    {
        abort_unless($opinion->project_id === $project->id, 404);
        abort_unless($opinion->user_id === Auth::id(), 403);

        $opinion->update(['body' => $request->validated()['body']]);

        return redirect()->route('projects.opinions.show', [$project, $opinion]);
    }

    public function destroy(Project $project, Opinion $opinion): RedirectResponse
    {
        abort_unless($opinion->project_id === $project->id, 404);
        abort_unless(
            $opinion->user_id === Auth::id() || $project->isAdminFor(Auth::user()),
            403
        );

        $opinion->delete();

        return redirect()->route('projects.opinions', $project);
    }
}
