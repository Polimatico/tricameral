<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.diff_title') }} · {{ $pullRequest->title }} · {{ $project->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0d0d0d] min-h-screen flex flex-col">

    <header class="flex items-center justify-between px-6 py-3 border-b border-white/10">
        <a href="{{ route('projects.merge.show', [$project, $pullRequest]) }}"
           class="text-white/60 text-sm font-medium tracking-wide hover:text-white/80 transition-colors">
            {{ __('messages.nav_merge_details') }}
        </a>
        <div class="flex items-center gap-3">
            <a href="{{ route('users.show', Auth::user()) }}" class="text-white/40 text-sm hover:text-white/60 transition-colors">
                {{ Auth::user()->displayName() }}
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="px-4 py-1.5 text-sm font-medium text-white/60 border border-white/15 rounded-lg hover:border-white/30 hover:text-white/80 transition-colors">
                    {{ __('messages.logout') }}
                </button>
            </form>
        </div>
    </header>

    <main class="flex-1 px-6 py-10 max-w-5xl mx-auto w-full">

        {{-- Intestazione --}}
        <div class="mb-6">
            <h1 class="text-lg font-bold text-white/85">{{ __('messages.diff_title') }}</h1>
            <p class="text-white/35 text-sm mt-1">
                {{ __('messages.diff_subtitle') }}
            </p>
            <div class="flex items-center gap-3 mt-3 text-xs text-white/25">
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-sm bg-white/10 border border-white/15"></span>
                    {{ __('messages.diff_original') }} <span class="text-white/40 font-medium">{{ $project->name }}</span>
                </span>
                <span>→</span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-sm" style="background: rgb(99 102 241 / 0.25); border: 1px solid rgb(99 102 241 / 0.35)"></span>
                    {{ __('messages.diff_source') }} <span class="text-white/40 font-medium">{{ $pullRequest->sourceProject->name }}</span>
                </span>
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-6 px-4 py-3 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400/80 text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('projects.merge.apply', [$project, $pullRequest]) }}">
            @csrf

            <div class="space-y-6 mb-8">
                @foreach ($diffs as $key => $diff)
                    @php
                        $oldChoice = old($key, 'original');
                        $oldContent = old($key . '_content', $diff['original']);
                    @endphp

                    <div class="border border-white/10 rounded-xl overflow-hidden">

                        {{-- File header --}}
                        <div class="px-4 py-2.5 border-b border-white/8 bg-white/2 flex items-center justify-between">
                            <span class="font-mono text-xs text-white/50 font-medium">{{ $diff['label'] }}</span>
                            @if ($diff['identical'])
                                <span class="text-white/20 text-xs">{{ __('messages.no_diff') }}</span>
                            @else
                                @php
                                    $addCount = collect($diff['hunks'])->flatMap(fn($h) => $h['lines'])->where('type', 'add')->count();
                                    $removeCount = collect($diff['hunks'])->flatMap(fn($h) => $h['lines'])->where('type', 'remove')->count();
                                @endphp
                                <span class="text-xs">
                                    <span class="text-emerald-400/70">+{{ $addCount }}</span>
                                    <span class="text-white/20 mx-1">/</span>
                                    <span class="text-red-400/60">-{{ $removeCount }}</span>
                                </span>
                            @endif
                        </div>

                        {{-- Diff hunks (solo se ci sono differenze) --}}
                        @if (! $diff['identical'])
                            <div class="font-mono text-xs overflow-x-auto" id="diff-view-{{ $key }}">
                                @foreach ($diff['hunks'] as $hunkIndex => $hunk)
                                    @if ($hunkIndex > 0)
                                        <div class="px-4 py-1.5 text-white/20 bg-white/1 border-y border-white/5 select-none">
                                            · · ·
                                        </div>
                                    @endif
                                    @foreach ($hunk['lines'] as $entry)
                                        @if ($entry['type'] === 'add')
                                            <div class="flex items-start">
                                                <span class="w-6 shrink-0 text-center py-0.5 select-none text-emerald-400/50 bg-emerald-500/8">+</span>
                                                <span class="flex-1 px-3 py-0.5 text-emerald-300/75 bg-emerald-500/8 whitespace-pre-wrap break-all">{{ $entry['line'] ?: ' ' }}</span>
                                            </div>
                                        @elseif ($entry['type'] === 'remove')
                                            <div class="flex items-start">
                                                <span class="w-6 shrink-0 text-center py-0.5 select-none text-red-400/50 bg-red-500/8">-</span>
                                                <span class="flex-1 px-3 py-0.5 text-red-300/60 bg-red-500/8 whitespace-pre-wrap break-all line-through decoration-red-400/30">{{ $entry['line'] ?: ' ' }}</span>
                                            </div>
                                        @else
                                            <div class="flex items-start">
                                                <span class="w-6 shrink-0 text-center py-0.5 select-none text-white/15"> </span>
                                                <span class="flex-1 px-3 py-0.5 text-white/30 whitespace-pre-wrap break-all">{{ $entry['line'] ?: ' ' }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                @endforeach
                            </div>
                        @else
                            <div class="px-5 py-4 text-white/25 text-xs" id="diff-view-{{ $key }}">
                                {{ __('messages.files_identical') }}
                            </div>
                        @endif

                        {{-- Editor manuale (nascosto finché non selezionato) --}}
                        <div id="manual-editor-{{ $key }}"
                             class="{{ $oldChoice === 'manual' ? '' : 'hidden' }} border-t border-white/8">
                            <div class="px-4 pt-3 pb-2 flex items-center justify-between">
                                <span class="text-white/30 text-xs">{{ __('messages.edit_manually_hint') }}</span>
                                <div class="flex items-center gap-2">
                                    <button type="button"
                                            onclick="fillContent('{{ $key }}', 'original')"
                                            class="px-2.5 py-1 text-xs text-white/35 border border-white/10 rounded hover:text-white/60 hover:border-white/25 transition-colors">
                                        ← {{ __('messages.original_label') }}
                                    </button>
                                    @if (! $diff['identical'])
                                        <button type="button"
                                                onclick="fillContent('{{ $key }}', 'source')"
                                                class="px-2.5 py-1 text-xs text-indigo-300/50 border border-indigo-500/15 rounded hover:text-indigo-300/80 hover:border-indigo-500/30 transition-colors">
                                            {{ __('messages.source_label') }} →
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="px-4 pb-4">
                                <textarea
                                    name="{{ $key }}_content"
                                    id="manual-content-{{ $key }}"
                                    rows="20"
                                    spellcheck="false"
                                    class="w-full font-mono text-xs bg-[#111] border border-white/10 rounded-lg px-3 py-2.5 text-white/65 placeholder-white/20 resize-y focus:outline-none focus:border-white/20 leading-relaxed"
                                    placeholder="{{ __('messages.write_final_ph') }}"
                                >{{ $oldContent }}</textarea>
                            </div>
                        </div>

                        {{-- Scelta versione --}}
                        <div class="px-4 py-3 border-t border-white/8 bg-white/1 flex flex-wrap items-center gap-x-6 gap-y-2">
                            <span class="text-white/35 text-xs font-medium">{{ __('messages.final_version') }}</span>

                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="{{ $key }}" value="original"
                                       {{ $oldChoice === 'original' ? 'checked' : '' }}
                                       onchange="toggleManual('{{ $key }}', false)"
                                       class="cursor-pointer">
                                <span class="text-xs text-white/50 group-hover:text-white/70 transition-colors">
                                    {{ __('messages.original_label') }} <span class="text-white/25">({{ $project->name }})</span>
                                </span>
                            </label>

                            @if (! $diff['identical'])
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="radio" name="{{ $key }}" value="source"
                                           {{ $oldChoice === 'source' ? 'checked' : '' }}
                                           onchange="toggleManual('{{ $key }}', false)"
                                           style="accent-color: rgb(129 140 248)">
                                    <span class="text-xs text-white/50 group-hover:text-white/70 transition-colors">
                                        {{ __('messages.source_label') }} <span class="text-white/25">({{ $pullRequest->sourceProject->name }})</span>
                                    </span>
                                </label>
                            @endif

                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="{{ $key }}" value="manual"
                                       {{ $oldChoice === 'manual' ? 'checked' : '' }}
                                       onchange="toggleManual('{{ $key }}', true)"
                                       style="accent-color: rgb(251 191 36)">
                                <span class="text-xs text-white/50 group-hover:text-white/70 transition-colors">
                                    {{ __('messages.edit_manually') }}
                                </span>
                            </label>
                        </div>

                    </div>
                @endforeach
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('projects.merge.show', [$project, $pullRequest]) }}"
                   class="px-4 py-2 text-sm font-medium text-white/40 border border-white/10 rounded-lg hover:text-white/60 hover:border-white/25 transition-colors">
                    {{ __('messages.cancel') }}
                </a>
                <button type="submit"
                        class="px-5 py-2 text-sm font-medium text-indigo-300/80 bg-indigo-500/10 border border-indigo-500/25 rounded-lg hover:bg-indigo-500/20 hover:border-indigo-500/40 hover:text-indigo-300 transition-colors">
                    {{ __('messages.apply_changes') }}
                </button>
            </div>
        </form>

    </main>

    <script>
        const fileContents = @json(
            collect($diffs)->map(fn($d) => [
                'original' => $d['original'],
                'source'   => $d['modified'],
            ])
        );

        function toggleManual(key, show) {
            const editor = document.getElementById('manual-editor-' + key);
            if (! editor) return;
            editor.classList.toggle('hidden', ! show);
            if (show) {
                const textarea = document.getElementById('manual-content-' + key);
                if (textarea && textarea.value === '') {
                    textarea.value = fileContents[key].original;
                }
                textarea && textarea.focus();
            }
        }

        function fillContent(key, version) {
            const textarea = document.getElementById('manual-content-' + key);
            if (textarea) {
                textarea.value = fileContents[key][version];
                textarea.focus();
            }
        }
    </script>

</body>
</html>
