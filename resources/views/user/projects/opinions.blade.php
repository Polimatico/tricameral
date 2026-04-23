<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.tab_opinions') }} · {{ $project->name }}</title>
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

        <nav class="flex items-center gap-1 mb-6 border-b border-white/8 pb-0">
            <a href="{{ route('projects.show', $project) }}"
               class="px-3 py-2 text-sm font-medium text-white/35 border-b-2 border-transparent -mb-px hover:text-white/60 transition-colors">
                {{ __('messages.tab_files') }}
            </a>
            <a href="{{ route('projects.opinions', $project) }}"
               class="px-3 py-2 text-sm font-medium text-white/80 border-b-2 border-white/60 -mb-px transition-colors">
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

        @if (session('success'))
            <div class="mb-4 px-4 py-2.5 bg-emerald-500/10 border border-emerald-500/20 rounded-lg text-emerald-400/80 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 px-4 py-2.5 bg-red-500/10 border border-red-500/20 rounded-lg text-red-400/80 text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- New opinion form --}}
        <div class="mb-6 border border-white/10 rounded-xl overflow-hidden">
            <div class="px-4 py-2.5 border-b border-white/8 bg-white/2">
                <span class="text-white/40 text-xs font-medium uppercase tracking-wider">{{ __('messages.new_opinion') }}</span>
            </div>
            <div class="p-4">
                <form method="POST" action="{{ route('projects.opinions.store', $project) }}">
                    @csrf
                    @include('user.partials.md_editor', [
                        'name' => 'body',
                        'value' => old('body', ''),
                        'rows' => 4,
                        'placeholder' => __('messages.opinion_body_ph'),
                        'size' => 'sm',
                    ])
                    @error('body')
                        <p class="text-red-400/80 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <div class="flex justify-end mt-3">
                        <button type="submit"
                            class="px-4 py-1.5 text-sm font-medium text-white/70 bg-white/6 border border-white/15 rounded-lg hover:bg-white/10 hover:border-white/25 transition-colors">
                            {{ __('messages.publish_opinion_btn') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Opinions list --}}
        <div class="border border-white/10 rounded-xl overflow-hidden">
            <div class="px-4 py-2.5 border-b border-white/8 bg-white/2 flex items-center justify-between">
                <span class="text-white/40 text-xs font-medium uppercase tracking-wider">{{ __('messages.opinions_header') }}</span>
                <span class="text-white/20 text-xs">{{ $opinions->count() }} {{ $opinions->count() === 1 ? __('messages.opinion_singular') : __('messages.opinion_plural') }}</span>
            </div>

            @if ($opinions->isEmpty())
                <div class="px-5 py-12 text-center text-white/25 text-sm">
                    {{ __('messages.no_opinions_yet') }}
                </div>
            @else
                <div class="divide-y divide-white/6">
                    @foreach ($opinions as $opinion)
                        @php
                            $score = $opinion->votes->where('value', 1)->count() - $opinion->votes->where('value', -1)->count();
                        @endphp
                        <a href="{{ route('projects.opinions.show', [$project, $opinion]) }}"
                           class="flex items-center gap-4 px-5 py-4 hover:bg-white/3 transition-colors group no-underline">
                            <div class="flex flex-col items-center shrink-0 w-8 gap-0.5">
                                <span class="text-xs font-medium {{ $score > 0 ? 'text-emerald-400/70' : ($score < 0 ? 'text-rose-400/60' : 'text-white/25') }}">
                                    {{ $score > 0 ? '+' : '' }}{{ $score }}
                                </span>
                                <span class="text-white/15 text-xs leading-none">▲▼</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-white/70 group-hover:text-white/90 transition-colors truncate">
                                    {{ Str::limit(strip_tags($opinion->body), 120) }}
                                </p>
                                <p class="text-xs mt-0.5 text-white/25">
                                    {{ $opinion->user->name }} · {{ $opinion->created_at->diffForHumans() }}
                                </p>
                            </div>
                            @if ($opinion->replies->isNotEmpty())
                                <div class="flex items-center gap-1 text-xs text-white/25 shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 2c-2.236 0-4.43.18-6.57.524C1.993 2.755 1 4.014 1 5.426v5.148c0 1.413.993 2.67 2.43 2.902.848.137 1.705.248 2.57.331v3.443a.75.75 0 0 0 1.28.53l3.58-3.579a.78.78 0 0 1 .527-.224 41.202 41.202 0 0 0 5.183-.5c1.437-.232 2.43-1.49 2.43-2.903V5.426c0-1.413-.993-2.67-2.43-2.902A41.289 41.289 0 0 0 10 2zm0 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2zM8 9a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm5 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $opinion->replies->count() }}</span>
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

    </main>

</body>
</html>
