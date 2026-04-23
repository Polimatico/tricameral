<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StarController extends Controller
{
    public function index(): View
    {
        $projects = Auth::user()
            ->starredProjects()
            ->withCount('stars')
            ->with('user')
            ->latest('stars.created_at')
            ->get();

        return view('user.profile.stars', ['projects' => $projects]);
    }

    public function toggle(Project $project): RedirectResponse
    {
        abort_unless($project->canView(Auth::user()), 403);

        $user = Auth::user();
        $existing = $user->stars()->where('project_id', $project->id)->first();

        if ($existing) {
            $existing->delete();
        } else {
            $user->stars()->create(['project_id' => $project->id]);
        }

        return back();
    }
}
