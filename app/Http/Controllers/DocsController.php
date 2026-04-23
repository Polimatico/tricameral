<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DocsController extends Controller
{
    /** @var array<string, string> Maps route slug to model column and display label */
    private const FILES = [
        'readme' => ['column' => 'readme', 'label' => 'README'],
        'conduct_code' => ['column' => 'conduct_code', 'label' => 'CODICE_DI_CONDOTTA'],
        'law_text' => ['column' => 'law_text', 'label' => 'TESTO_DELLA_LEGGE'],
    ];

    public function show(Project $project, string $file): View
    {
        abort_unless($project->user_id === Auth::id(), 403);
        abort_unless(isset(self::FILES[$file]), 404);

        $meta = self::FILES[$file];
        $content = $project->{$meta['column']} ?? '';

        return view('user.projects.editor', [
            'content' => $content,
            'project' => $project,
            'file' => $file,
            'fileLabel' => $meta['label'],
            'saveUrl' => route('docs.store', [$project, $file]),
            'backUrl' => route('projects.show', $project),
        ]);
    }

    public function store(Request $request, Project $project, string $file): JsonResponse
    {
        abort_unless($project->user_id === Auth::id(), 403);
        abort_unless(isset(self::FILES[$file]), 404);

        $validated = $request->validate(['markdown' => ['required', 'string']]);

        $column = self::FILES[$file]['column'];
        $project->update([$column => $validated['markdown']]);

        return response()->json(['saved' => true]);
    }
}
