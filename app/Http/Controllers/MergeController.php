<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\PullRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MergeController extends Controller
{
    /** @var array<string, array{column: string, label: string}> */
    private const FILES = [
        'readme' => ['column' => 'readme', 'label' => 'README'],
        'conduct_code' => ['column' => 'conduct_code', 'label' => 'CODICE_DI_CONDOTTA'],
        'law_text' => ['column' => 'law_text', 'label' => 'TESTO_DELLA_LEGGE'],
    ];

    public function diff(Project $project, PullRequest $pullRequest): View|RedirectResponse
    {
        if ($project->user_id !== Auth::id()) {
            return redirect()->route('projects.show', $project)
                ->with('error', 'Solo il proprietario può accedere alla sezione Merge.');
        }

        if (! $pullRequest->isAccepted() || $pullRequest->project_id !== $project->id) {
            return redirect()->route('projects.merge', $project)
                ->with('error', 'Merge non trovato.');
        }

        $pullRequest->load('sourceProject');

        if (! $pullRequest->sourceProject) {
            return redirect()->route('projects.merge.show', [$project, $pullRequest])
                ->with('error', 'La legge sorgente non è più disponibile.');
        }

        $source = $pullRequest->sourceProject;
        $diffs = [];

        foreach (self::FILES as $key => $meta) {
            $original = $project->{$meta['column']} ?? '';
            $modified = $source->{$meta['column']} ?? '';
            $diffs[$key] = [
                'label' => $meta['label'],
                'original' => $original,
                'modified' => $modified,
                'identical' => $original === $modified,
                'hunks' => $original === $modified ? [] : $this->buildHunks($original, $modified),
            ];
        }

        $isStarred = Auth::user()->stars()->where('project_id', $project->id)->exists();

        return view('user.projects.merge_diff', [
            'project' => $project,
            'pullRequest' => $pullRequest,
            'diffs' => $diffs,
            'isStarred' => $isStarred,
        ]);
    }

    public function apply(Request $request, Project $project, PullRequest $pullRequest): RedirectResponse
    {
        if ($project->user_id !== Auth::id()) {
            return redirect()->route('projects.show', $project)
                ->with('error', 'Solo il proprietario può accedere alla sezione Merge.');
        }

        if (! $pullRequest->isAccepted() || $pullRequest->project_id !== $project->id) {
            return redirect()->route('projects.merge', $project)
                ->with('error', 'Merge non trovato.');
        }

        $pullRequest->load('sourceProject');

        if (! $pullRequest->sourceProject) {
            return redirect()->route('projects.merge.show', [$project, $pullRequest])
                ->with('error', 'La legge sorgente non è più disponibile.');
        }

        $validated = $request->validate([
            'readme' => ['required', 'in:original,source,manual'],
            'readme_content' => ['nullable', 'string', 'required_if:readme,manual'],
            'conduct_code' => ['required', 'in:original,source,manual'],
            'conduct_code_content' => ['nullable', 'string', 'required_if:conduct_code,manual'],
            'law_text' => ['required', 'in:original,source,manual'],
            'law_text_content' => ['nullable', 'string', 'required_if:law_text,manual'],
        ]);

        $source = $pullRequest->sourceProject;
        $updates = [];

        foreach (self::FILES as $key => $meta) {
            if ($validated[$key] === 'source') {
                $updates[$meta['column']] = $source->{$meta['column']} ?? '';
            } elseif ($validated[$key] === 'manual') {
                $updates[$meta['column']] = $validated[$key.'_content'] ?? '';
            }
        }

        if (! empty($updates)) {
            $project->update($updates);
        }

        $changedCount = count($updates);
        $message = $changedCount > 0
            ? "Modifiche applicate: {$changedCount} ".($changedCount === 1 ? 'file aggiornato' : 'file aggiornati').'.'
            : 'Nessuna modifica applicata.';

        return redirect()->route('projects.merge.show', [$project, $pullRequest])
            ->with('success', $message);
    }

    /**
     * Builds diff hunks (groups of context lines + changes) between two texts.
     *
     * @return array<int, array{lines: array<int, array{type: string, line: string}>}>
     */
    private function buildHunks(string $original, string $modified, int $context = 3): array
    {
        $diff = $this->computeDiff($original, $modified);
        $total = count($diff);

        $changedIndices = [];
        foreach ($diff as $i => $entry) {
            if ($entry['type'] !== 'context') {
                $changedIndices[] = $i;
            }
        }

        if (empty($changedIndices)) {
            return [];
        }

        $ranges = [];
        $start = max(0, $changedIndices[0] - $context);
        $end = min($total - 1, $changedIndices[0] + $context);

        foreach ($changedIndices as $idx) {
            if ($idx - $context <= $end + 1) {
                $end = min($total - 1, $idx + $context);
            } else {
                $ranges[] = [$start, $end];
                $start = max(0, $idx - $context);
                $end = min($total - 1, $idx + $context);
            }
        }
        $ranges[] = [$start, $end];

        $hunks = [];
        foreach ($ranges as $range) {
            $lines = [];
            for ($i = $range[0]; $i <= $range[1]; $i++) {
                $lines[] = $diff[$i];
            }
            $hunks[] = ['lines' => $lines];
        }

        return $hunks;
    }

    /**
     * LCS-based line diff between two texts.
     *
     * @return array<int, array{type: string, line: string}>
     */
    private function computeDiff(string $original, string $modified): array
    {
        $a = explode("\n", $original);
        $b = explode("\n", $modified);
        $m = count($a);
        $n = count($b);

        $lcs = array_fill(0, $m + 1, array_fill(0, $n + 1, 0));

        for ($i = 1; $i <= $m; $i++) {
            for ($j = 1; $j <= $n; $j++) {
                if ($a[$i - 1] === $b[$j - 1]) {
                    $lcs[$i][$j] = $lcs[$i - 1][$j - 1] + 1;
                } else {
                    $lcs[$i][$j] = max($lcs[$i - 1][$j], $lcs[$i][$j - 1]);
                }
            }
        }

        $diff = [];
        $i = $m;
        $j = $n;

        while ($i > 0 || $j > 0) {
            if ($i > 0 && $j > 0 && $a[$i - 1] === $b[$j - 1]) {
                array_unshift($diff, ['type' => 'context', 'line' => $a[$i - 1]]);
                $i--;
                $j--;
            } elseif ($j > 0 && ($i === 0 || $lcs[$i][$j - 1] >= $lcs[$i - 1][$j])) {
                array_unshift($diff, ['type' => 'add', 'line' => $b[$j - 1]]);
                $j--;
            } else {
                array_unshift($diff, ['type' => 'remove', 'line' => $a[$i - 1]]);
                $i--;
            }
        }

        return $diff;
    }
}
