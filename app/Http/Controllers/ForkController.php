<?php

namespace App\Http\Controllers;

use App\Enums\ForkListing;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ForkController extends Controller
{
    public function create(Project $project): View|RedirectResponse
    {
        $user = Auth::user();

        if (! $project->canView($user)) {
            return redirect()->route('projects.index')
                ->with('error', 'Non hai i permessi per visualizzare questa legge.');
        }

        $isAdmin = $project->isAdminFor($user);
        $canFork = $project->canFork($user);

        $forksQuery = $project->forks()->with('user');

        if (! $isAdmin) {
            if ($project->fork_listing === ForkListing::Manual) {
                $forksQuery->where('fork_visible', true);
            }
            $forksQuery->accessibleBy($user);
        }

        $forks = $forksQuery->latest()->get();

        $isStarred = $user->stars()->where('project_id', $project->id)->exists();

        return view('user.projects.fork', [
            'project' => $project,
            'forks' => $forks,
            'isAdmin' => $isAdmin,
            'canFork' => $canFork,
            'isStarred' => $isStarred,
        ]);
    }

    public function store(Project $project): RedirectResponse
    {
        $user = Auth::user();

        if (! $project->canFork($user)) {
            return redirect()->route('projects.show', $project)
                ->with('error', 'Non hai i permessi per fare un fork di questa legge.');
        }

        $fork = $user->projects()->create([
            'name' => $project->name,
            'description' => $project->description,
            'readme' => $project->readme,
            'conduct_code' => $project->conduct_code,
            'law_text' => $project->law_text,
            'visibility' => $project->visibility,
            'fork_permission' => $project->fork_permission,
            'forked_from_id' => $project->id,
        ]);

        return redirect()->route('projects.show', $fork)
            ->with('success', 'Fork creato con successo.');
    }

    public function updateListingMode(Request $request, Project $project): RedirectResponse
    {
        $user = Auth::user();

        if (! $project->isAdminFor($user)) {
            return redirect()->route('projects.fork', $project)
                ->with('error', 'Solo gli admin possono modificare le impostazioni dei fork.');
        }

        $validated = $request->validate([
            'fork_listing' => ['required', 'in:automatic,manual'],
        ]);

        $project->update(['fork_listing' => $validated['fork_listing']]);

        return redirect()->route('projects.fork', $project)
            ->with('success', 'Modalità di visibilità fork aggiornata.');
    }

    public function updateForkVisibility(Request $request, Project $project, Project $fork): RedirectResponse
    {
        $user = Auth::user();

        if (! $project->isAdminFor($user)) {
            return redirect()->route('projects.fork', $project)
                ->with('error', 'Solo gli admin possono modificare la visibilità dei fork.');
        }

        if ($fork->forked_from_id !== $project->id) {
            return redirect()->route('projects.fork', $project)
                ->with('error', 'Fork non trovato.');
        }

        $fork->update(['fork_visible' => ! $fork->fork_visible]);

        return redirect()->route('projects.fork', $project)
            ->with('success', 'Visibilità del fork aggiornata.');
    }
}
