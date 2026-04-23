<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.tab_team') }} · {{ $project->name }}</title>
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
               class="px-3 py-2 text-sm font-medium text-white/35 border-b-2 border-transparent -mb-px hover:text-white/60 transition-colors">
                {{ __('messages.tab_fork') }}
            </a>
            <a href="{{ route('projects.team', $project) }}"
               class="px-3 py-2 text-sm font-medium text-white/80 border-b-2 border-white/60 -mb-px transition-colors">
                {{ __('messages.tab_team') }}
            </a>
            <a href="{{ route('projects.settings', $project) }}"
               class="px-3 py-2 text-sm font-medium text-white/35 border-b-2 border-transparent -mb-px hover:text-white/60 transition-colors">
                {{ __('messages.tab_settings') }}
            </a>
        </nav>

        <div class="space-y-6">

            {{-- Membri --}}
            <div class="border border-white/10 rounded-xl overflow-hidden">
                <div class="px-4 py-2.5 border-b border-white/8 bg-white/2">
                    <span class="text-white/40 text-xs font-medium uppercase tracking-wider">{{ __('messages.team_section_members') }}</span>
                </div>

                <div class="divide-y divide-white/8">

                    {{-- Proprietario --}}
                    <div class="flex items-center justify-between px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-full bg-white/8 flex items-center justify-center text-white/40 text-xs font-medium">
                                {{ mb_substr($project->user->name, 0, 1) }}
                            </div>
                            <div>
                                <a href="{{ route('users.show', $project->user) }}" class="text-white/70 text-sm hover:text-white/90 transition-colors">{{ $project->user->name }}</a>
                                <p class="text-white/30 text-xs">{{ $project->user->email }}</p>
                            </div>
                        </div>
                        <span class="text-white/25 text-xs border border-white/8 rounded px-2 py-0.5">{{ __('messages.team_owner') }}</span>
                    </div>

                    @php
                        $roleOrder = [\App\Enums\ProjectRole::Admin, \App\Enums\ProjectRole::Editor, \App\Enums\ProjectRole::Viewer];
                    @endphp

                    @foreach ($roleOrder as $role)
                        @if ($members->has($role->value))
                            @foreach ($members[$role->value] as $member)
                                <div class="flex items-center justify-between px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-7 h-7 rounded-full bg-white/8 flex items-center justify-center text-white/40 text-xs font-medium">
                                            {{ mb_substr($member->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('users.show', $member->user) }}" class="text-white/70 text-sm hover:text-white/90 transition-colors">{{ $member->user->name }}</a>
                                            <p class="text-white/30 text-xs">{{ $member->user->email }}</p>
                                        </div>
                                    </div>
                                    <span class="text-white/25 text-xs border border-white/8 rounded px-2 py-0.5">{{ $member->role->label() }}</span>
                                </div>
                            @endforeach
                        @endif
                    @endforeach

                    @if ($members->isEmpty())
                        <div class="px-5 py-8 text-center text-white/25 text-sm">
                            {{ __('messages.team_no_members') }}
                        </div>
                    @endif

                </div>
            </div>

            {{-- Contributori --}}
            <div class="border border-white/10 rounded-xl overflow-hidden">
                <div class="px-4 py-2.5 border-b border-white/8 bg-white/2">
                    <span class="text-white/40 text-xs font-medium uppercase tracking-wider">{{ __('messages.team_section_contributors') }}</span>
                </div>

                <div class="divide-y divide-white/8">
                    @forelse ($contributors as $contributor)
                        <div class="flex items-center justify-between px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-full bg-white/8 flex items-center justify-center text-white/40 text-xs font-medium">
                                    {{ mb_substr($contributor->name, 0, 1) }}
                                </div>
                                <div>
                                    <a href="{{ route('users.show', $contributor) }}" class="text-white/70 text-sm hover:text-white/90 transition-colors">{{ $contributor->name }}</a>
                                    <p class="text-white/30 text-xs">{{ $contributor->email }}</p>
                                </div>
                            </div>
                            <span class="text-white/20 text-xs">
                                <svg class="inline-block mr-1 -mt-px" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                PR
                            </span>
                        </div>
                    @empty
                        <div class="px-5 py-8 text-center text-white/25 text-sm">
                            {{ __('messages.team_no_contributors') }}
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </main>

</body>
</html>
