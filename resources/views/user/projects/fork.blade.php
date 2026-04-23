<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.tab_fork') }} · {{ $project->name }}</title>
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

        @if (session('success'))
            <div class="mb-6 px-4 py-3 rounded-lg bg-white/5 border border-white/10 text-white/60 text-sm">
                {{ session('success') }}
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
               class="px-3 py-2 text-sm font-medium text-white/35 border-b-2 border-transparent -mb-px hover:text-white/60 transition-colors">
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
               class="px-3 py-2 text-sm font-medium text-white/80 border-b-2 border-white/60 -mb-px transition-colors">
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

        {{-- Lista fork --}}
        <div class="border border-white/10 rounded-xl overflow-hidden mb-6">
            <div class="px-4 py-2.5 border-b border-white/8 bg-white/2 flex items-center justify-between">
                <span class="text-white/40 text-xs font-medium uppercase tracking-wider">{{ __('messages.fork_list_header') }}</span>
                <div class="flex items-center gap-3">
                    <span class="text-white/25 text-xs">{{ $forks->count() }} {{ __('messages.tab_fork') }}</span>
                    @if ($canFork)
                        <button
                            onclick="document.getElementById('modal-fork').classList.remove('hidden')"
                            class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium text-white/60 border border-white/15 rounded-md hover:border-white/30 hover:text-white/80 transition-colors">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            {{ __('messages.create_fork_btn') }}
                        </button>
                    @endif
                </div>
            </div>

            @forelse ($forks as $fork)
                <div class="flex items-center justify-between px-5 py-3 border-b border-white/8 last:border-b-0 {{ $isAdmin && ! $fork->fork_visible ? 'opacity-50' : '' }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <svg class="text-white/20 shrink-0" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="18" r="3"/><circle cx="6" cy="6" r="3"/><circle cx="18" cy="6" r="3"/>
                            <path d="M6 9v2a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V9"/><line x1="12" y1="12" x2="12" y2="15"/>
                        </svg>
                        <div class="min-w-0">
                            <a href="{{ route('projects.show', $fork) }}" class="text-white/70 text-sm hover:text-white/90 transition-colors font-medium truncate block">
                                {{ $fork->user->displayName() }}/{{ $fork->name }}
                            </a>
                            <p class="text-white/25 text-xs mt-0.5">{{ __('messages.fork_created_at', ['time' => $fork->created_at->diffForHumans()]) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        @if ($isAdmin && ! $fork->fork_visible)
                            <span class="text-xs text-white/20 border border-white/8 rounded px-2 py-0.5">{{ __('messages.fork_hidden_label') }}</span>
                        @endif
                        @if ($isAdmin && $project->fork_listing === \App\Enums\ForkListing::Manual)
                            <form method="POST" action="{{ route('projects.fork.visibility', [$project, $fork]) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="px-2.5 py-1 text-xs border rounded transition-colors {{ $fork->fork_visible ? 'text-green-400/60 border-green-500/20 hover:border-green-500/40' : 'text-white/30 border-white/10 hover:border-white/25 hover:text-white/60' }}">
                                    {{ $fork->fork_visible ? __('messages.fork_visible_label') : __('messages.fork_show_label') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-5 py-8 text-center text-white/25 text-sm">
                    {{ __('messages.no_forks_yet') }}
                </div>
            @endforelse
        </div>

    </main>

    {{-- Modale: crea fork --}}
    @if ($canFork)
        <div id="modal-fork" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
            <div
                class="absolute inset-0 bg-black/70"
                onclick="document.getElementById('modal-fork').classList.add('hidden')">
            </div>

            <div class="relative w-full max-w-md bg-[#141414] border border-white/12 rounded-2xl overflow-hidden shadow-2xl">
                <div class="px-5 py-4 border-b border-white/8 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="text-white/30" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="18" r="3"/><circle cx="6" cy="6" r="3"/><circle cx="18" cy="6" r="3"/>
                            <path d="M6 9v2a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V9"/><line x1="12" y1="12" x2="12" y2="15"/>
                        </svg>
                        <h2 class="text-white/80 text-sm font-semibold">{{ __('messages.fork_modal_title') }}</h2>
                    </div>
                    <button
                        onclick="document.getElementById('modal-fork').classList.add('hidden')"
                        class="text-white/25 hover:text-white/60 transition-colors text-lg leading-none">
                        ✕
                    </button>
                </div>

                <div class="px-5 py-5">
                    <p class="text-white/35 text-sm mb-5">
                        {{ __('messages.fork_creating_copy') }}
                        <span class="text-white/60">{{ $project->user->displayName() }}/{{ $project->name }}</span>
                        {{ __('messages.fork_on_account') }}
                    </p>

                    <div class="border border-white/8 rounded-lg overflow-hidden mb-5">
                        @foreach ([['README', $project->readme], ['CODICE_DI_CONDOTTA', $project->conduct_code], ['TESTO_DELLA_LEGGE', $project->law_text]] as [$label, $content])
                            <div class="flex items-center justify-between px-4 py-2.5 border-b border-white/6 last:border-b-0">
                                <span class="text-white/40 text-xs">{{ $label }}</span>
                                @if ($content)
                                    <span class="text-xs text-green-400/60 border border-green-500/15 rounded px-2 py-0.5">{{ __('messages.content_present') }}</span>
                                @else
                                    <span class="text-xs text-white/20 border border-white/8 rounded px-2 py-0.5">{{ __('messages.content_empty') }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <form method="POST" action="{{ route('projects.fork.store', $project) }}">
                        @csrf
                        <div class="flex items-center justify-end gap-3">
                            <button
                                type="button"
                                onclick="document.getElementById('modal-fork').classList.add('hidden')"
                                class="px-4 py-2 text-sm text-white/35 hover:text-white/60 transition-colors">
                                {{ __('messages.cancel') }}
                            </button>
                            <button type="submit"
                                class="px-5 py-2 text-sm font-medium text-white/80 border border-white/20 rounded-lg hover:border-white/40 hover:text-white transition-colors">
                                {{ __('messages.create_fork_btn') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

</body>
</html>
