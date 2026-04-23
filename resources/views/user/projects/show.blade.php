<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $project->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0d0d0d] min-h-screen flex flex-col">

    <header class="flex items-center justify-between px-6 py-3 border-b border-white/10">
        <a href="{{ route('projects.index') }}" class="text-white/60 text-sm font-medium tracking-wide hover:text-white/80 transition-colors">
            {{ __('messages.nav_laws') }}
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

    <main class="flex-1 px-6 py-10 max-w-4xl mx-auto w-full">

        @if (session('error'))
            <div class="mb-6 px-4 py-3 rounded-lg bg-red-500/8 border border-red-500/15 text-red-400/80 text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Intestazione legge --}}
        <div class="mb-8">
            <div class="flex items-start gap-3 mb-2">
                <div class="mt-0.5 text-white/20">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 3h18v18H3z"/><path d="M3 9h18"/><path d="M9 21V9"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white/85">{{ $project->name }}</h1>
                    @if ($project->description)
                        <p class="text-white/40 text-sm mt-1">{{ $project->description }}</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-4 mt-3 text-xs text-white/25">
                <span>{{ __('messages.created_at', ['time' => $project->created_at->diffForHumans()]) }}</span>
                <span>·</span>
                <a href="{{ route('users.show', $project->user) }}" class="hover:text-white/50 transition-colors">{{ $project->user->displayName() }}</a>
                @if ($project->forkedFrom)
                    <span>·</span>
                    <span>{{ __('messages.fork_of') }} <a href="{{ route('projects.show', $project->forkedFrom) }}" class="hover:text-white/50 transition-colors">{{ $project->forkedFrom->name }}</a></span>
                @endif
            </div>

            {{-- Azioni: stella --}}
            <div class="flex items-center gap-2 mt-3">
                <form method="POST" action="{{ route('projects.star', $project) }}">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs rounded-lg border transition-colors {{ $isStarred ? 'text-yellow-400 border-yellow-400/30 hover:border-yellow-400/50' : 'text-white/30 border-white/10 hover:text-white/60 hover:border-white/25' }}">
                        ★ {{ $isStarred ? __('messages.star_remove') : __('messages.star_add') }}
                    </button>
                </form>
            </div>
        </div>

        {{-- Navigazione sezioni --}}
        <nav class="flex items-center gap-1 mb-6 border-b border-white/8 pb-0">
            <a href="{{ route('projects.show', $project) }}"
               class="px-3 py-2 text-sm font-medium text-white/80 border-b-2 border-white/60 -mb-px transition-colors">
                {{ __('messages.tab_files') }}
            </a>
            <a href="{{ route('projects.opinions', $project) }}"
               class="px-3 py-2 text-sm font-medium text-white/35 border-b-2 border-transparent -mb-px hover:text-white/60 transition-colors">
                {{ __('messages.tab_opinions') }}
            </a>
            <a href="{{ route('projects.issues', $project) }}"
               class="px-3 py-2 text-sm font-medium text-white/35 border-b-2 border-transparent -mb-px hover:text-white/60 transition-colors">
                {{ __('messages.tab_issues') }}
            </a>
            <a href="{{ route('projects.pull', $project) }}"
               class="px-3 py-2 text-sm font-medium text-white/35 border-b-2 border-transparent -mb-px hover:text-white/60 transition-colors">
                {{ __('messages.tab_pull') }}
            </a>
            <a href="{{ route('projects.merge', $project) }}"
               class="px-3 py-2 text-sm font-medium text-white/35 border-b-2 border-transparent -mb-px hover:text-white/60 transition-colors">
                {{ __('messages.tab_merge') }}
            </a>
            <a href="{{ route('projects.fork', $project) }}"
               class="px-3 py-2 text-sm font-medium text-white/35 border-b-2 border-transparent -mb-px hover:text-white/60 transition-colors">
                {{ __('messages.tab_fork') }}
            </a>
            <a href="{{ route('projects.team', $project) }}"
               class="px-3 py-2 text-sm font-medium text-white/35 border-b-2 border-transparent -mb-px hover:text-white/60 transition-colors">
                {{ __('messages.tab_team') }}
            </a>
            <a href="{{ route('projects.settings', $project) }}"
               class="px-3 py-2 text-sm font-medium text-white/35 border-b-2 border-transparent -mb-px hover:text-white/60 transition-colors">
                {{ __('messages.tab_settings') }}
            </a>
        </nav>

        {{-- Lista file --}}
        <div class="border border-white/10 rounded-xl overflow-hidden mb-6">
            <div class="px-4 py-2.5 border-b border-white/8 bg-white/2 flex items-center justify-between">
                <span class="text-white/40 text-xs font-medium uppercase tracking-wider">{{ __('messages.files_header') }}</span>
                <span class="text-white/20 text-xs">{{ __('messages.files_count') }}</span>
            </div>

            <div class="divide-y divide-white/8">
                @php
                    $files = [
                        ['key' => 'readme',   'slug' => 'readme',       'label' => __('messages.file_readme'),   'icon' => 'book'],
                        ['key' => 'conduct',  'slug' => 'conduct_code', 'label' => __('messages.file_conduct'),  'icon' => 'shield'],
                        ['key' => 'law',      'slug' => 'law_text',     'label' => __('messages.file_law_text'), 'icon' => 'scale'],
                    ];
                @endphp

                @foreach ($files as $f)
                    <div class="flex items-center gap-3 px-4 py-3 hover:bg-white/3 transition-colors group">
                        <button onclick="showFile('{{ $f['key'] }}')" class="flex items-center gap-3 flex-1 text-left min-w-0">
                            @if ($f['icon'] === 'book')
                                <svg class="text-white/25 shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                </svg>
                            @elseif ($f['icon'] === 'shield')
                                <svg class="text-white/25 shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                </svg>
                            @elseif ($f['icon'] === 'scale')
                                <svg class="text-white/25 shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 3v18"/><path d="M3 9l9-6 9 6"/><path d="M3 15h18"/><path d="M3 9h18"/><path d="M21 15l-9 6-9-6"/>
                                </svg>
                            @endif
                            <span class="text-white/70 text-sm group-hover:text-white/90 transition-colors font-medium">{{ $f['label'] }}</span>
                        </button>
                        <a href="{{ route('docs.show', [$project, $f['slug']]) }}"
                            class="shrink-0 px-2.5 py-1 text-xs text-white/35 border border-white/10 rounded-md hover:text-white/70 hover:border-white/25 transition-colors opacity-0 group-hover:opacity-100">
                            {{ __('messages.file_edit_btn') }}
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Pannello file selezionato --}}
        <div id="file-panel" class="hidden border border-white/10 rounded-xl overflow-hidden">

            {{-- README --}}
            <div id="file-readme" class="hidden">
                <div class="px-4 py-3 border-b border-white/8 bg-white/2 flex items-center justify-between">
                    <span class="text-white/60 text-sm font-medium">{{ __('messages.file_readme') }}</span>
                    <button onclick="hideFile()" class="text-white/30 hover:text-white/60 transition-colors text-sm">✕</button>
                </div>
                @if ($project->readme)
                    <div id="preview-readme" class="tiptap-preview px-5 py-4 text-white/70 text-sm leading-relaxed"></div>
                @else
                    <div class="px-5 py-8 text-center text-white/25 text-sm">
                        {{ __('messages.no_content_yet') }}
                    </div>
                @endif
            </div>

            {{-- Codice di condotta --}}
            <div id="file-conduct" class="hidden">
                <div class="px-4 py-3 border-b border-white/8 bg-white/2 flex items-center justify-between">
                    <span class="text-white/60 text-sm font-medium">{{ __('messages.file_conduct') }}</span>
                    <button onclick="hideFile()" class="text-white/30 hover:text-white/60 transition-colors text-sm">✕</button>
                </div>
                @if ($project->conduct_code)
                    <div id="preview-conduct" class="tiptap-preview px-5 py-4 text-white/70 text-sm leading-relaxed"></div>
                @else
                    <div class="px-5 py-8 text-center text-white/25 text-sm">
                        {{ __('messages.no_content_yet') }}
                    </div>
                @endif
            </div>

            {{-- Testo della legge --}}
            <div id="file-law" class="hidden">
                <div class="px-4 py-3 border-b border-white/8 bg-white/2 flex items-center justify-between">
                    <span class="text-white/60 text-sm font-medium">{{ __('messages.file_law_text') }}</span>
                    <button onclick="hideFile()" class="text-white/30 hover:text-white/60 transition-colors text-sm">✕</button>
                </div>
                @if ($project->law_text)
                    <div id="preview-law" class="tiptap-preview px-5 py-4 text-white/70 text-sm leading-relaxed"></div>
                @else
                    <div class="px-5 py-8 text-center text-white/25 text-sm">
                        {{ __('messages.no_content_yet') }}
                    </div>
                @endif
            </div>

        </div>

    </main>

    <script>
        window.__projectFiles = {
            readme:  @json($project->readme ?? ''),
            conduct: @json($project->conduct_code ?? ''),
            law:     @json($project->law_text ?? ''),
        };

        const fileMap = { readme: 'file-readme', conduct: 'file-conduct', law: 'file-law' };

        function showFile(key) {
            Object.values(fileMap).forEach(id => document.getElementById(id).classList.add('hidden'));
            document.getElementById('file-panel').classList.remove('hidden');
            document.getElementById(fileMap[key]).classList.remove('hidden');
            document.getElementById('file-panel').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function hideFile() {
            document.getElementById('file-panel').classList.add('hidden');
        }
    </script>

</body>
</html>
