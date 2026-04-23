<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddProjectMemberRequest;
use App\Http\Requests\UpdateProjectMemberRoleRequest;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ProjectMemberController extends Controller
{
    public function store(AddProjectMemberRequest $request, Project $project): RedirectResponse
    {
        $user = User::where('email', $request->validated('email'))->firstOrFail();

        abort_if($user->id === Auth::id(), 422, 'Non puoi aggiungere te stesso come membro.');

        $project->members()->updateOrCreate(
            ['user_id' => $user->id],
            ['role' => $request->validated('role')],
        );

        return redirect()->route('projects.settings', $project)
            ->with('success', 'Membro aggiunto con successo.');
    }

    public function update(UpdateProjectMemberRoleRequest $request, Project $project, User $user): RedirectResponse
    {
        $project->members()
            ->where('user_id', $user->id)
            ->update(['role' => $request->validated('role')]);

        return redirect()->route('projects.settings', $project)
            ->with('success', 'Ruolo aggiornato.');
    }

    public function destroy(Project $project, User $user): RedirectResponse
    {
        abort_unless($project->user_id === Auth::id(), 403);

        $project->members()->where('user_id', $user->id)->delete();

        return redirect()->route('projects.settings', $project)
            ->with('success', 'Membro rimosso.');
    }
}
