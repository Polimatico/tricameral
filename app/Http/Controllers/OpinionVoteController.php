<?php

namespace App\Http\Controllers;

use App\Models\Opinion;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OpinionVoteController extends Controller
{
    public function store(Request $request, Project $project, Opinion $opinion): RedirectResponse
    {
        abort_unless($opinion->project_id === $project->id, 404);
        abort_unless($project->canView(Auth::user()), 403);

        $value = $request->input('value') === 'up' ? 1 : -1;

        $existing = $opinion->votes()->where('user_id', Auth::id())->first();

        if ($existing) {
            if ($existing->value === $value) {
                // Same vote → remove it (toggle off)
                $existing->delete();
            } else {
                // Opposite vote → switch
                $existing->update(['value' => $value]);
            }
        } else {
            $opinion->votes()->create([
                'user_id' => Auth::id(),
                'value' => $value,
            ]);
        }

        return redirect()->route('projects.opinions.show', [$project, $opinion]);
    }
}
