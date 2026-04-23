<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.tab_pull') }} · {{ $project->name }}</title>
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
               class="px-3 py-2 text-sm font-medium text-white/35 border-b-2 border-transparent -mb-px hover:text-white/60 transition-colors">
                {{ __('messages.tab_opinions') }}
            </a>
            <a href="{{ route('projects.issues', $project) }}"
               class="px-3 py-2 text-sm font-medium text-white/35 border-b-2 border-transparent -mb-px hover:text-white/60 transition-colors">
                {{ __('messages.tab_issues') }}
            </a>
            <a href="{{ route('projects.pull', $project) }}"
               class="px-3 py-2 text-sm font-medium text-white/80 border-b-2 border-white/60 -mb-px transition-colors">
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

        {{-- Form nuova pull request --}}
        @if ($canCreatePull)
            <div class="mb-6 border border-white/10 rounded-xl overflow-hidden">
                <div class="px-4 py-2.5 border-b border-white/8 bg-white/2">
                    <span class="text-white/40 text-xs font-medium uppercase tracking-wider">{{ __('messages.open_new_pull') }}</span>
                </div>
                <div class="p-4">
                    <form method="POST" action="{{ route('projects.pull.store', $project) }}">
                        @csrf
                        <input
                            type="text"
                            name="title"
                            placeholder="{{ __('messages.pull_title_ph') }}"
                            value="{{ old('title') }}"
                            class="w-full bg-white/4 border border-white/10 rounded-lg px-3 py-2 text-sm text-white/80 placeholder-white/20 focus:outline-none focus:border-white/25 mb-3"
                        >
                        @error('title')
                            <p class="text-red-400/80 text-xs mb-2">{{ $message }}</p>
                        @enderror

                        @if ($myProjects->isNotEmpty())
                            <div class="mb-3">
                                <label class="block text-white/40 text-xs mb-1.5">{{ __('messages.source_law_label') }}</label>
                                <select
                                    name="source_project_id"
                                    class="w-full bg-white/4 border border-white/10 rounded-lg px-3 py-2 text-sm text-white/60 focus:outline-none focus:border-white/25 transition-colors"
                                >
                                    <option value="">{{ __('messages.no_source_law') }}</option>
                                    @foreach ($myProjects as $myProject)
                                        <option value="{{ $myProject->id }}" {{ old('source_project_id') == $myProject->id ? 'selected' : '' }}>
                                            {{ $myProject->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @include('user.partials.md_editor', [
                            'name' => 'body',
                            'value' => old('body', ''),
                            'rows' => 5,
                            'placeholder' => __('messages.pull_body_ph'),
                            'size' => 'sm',
                        ])
                        @error('body')
                            <p class="text-red-400/80 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <div class="flex justify-end mt-3">
                            <button type="submit"
                                class="px-4 py-1.5 text-sm font-medium text-white/70 bg-white/6 border border-white/15 rounded-lg hover:bg-white/10 hover:border-white/25 transition-colors">
                                {{ __('messages.open_pull_btn') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- Admin: gestione tag --}}
        @if ($project->isAdminFor(Auth::user()))
            <div class="mb-6 border border-white/10 rounded-xl overflow-hidden">
                <div class="px-4 py-2.5 border-b border-white/8 bg-white/2">
                    <span class="text-white/40 text-xs font-medium uppercase tracking-wider">{{ __('messages.manage_tags') }}</span>
                </div>
                <div class="p-4">
                    @if ($projectTags->isNotEmpty())
                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach ($projectTags as $tag)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                                      style="background-color: {{ $tag->color }}22; color: {{ $tag->color }}; border: 1px solid {{ $tag->color }}44;">
                                    {{ $tag->label }}
                                    <form method="POST" action="{{ route('projects.pull_tags.destroy', [$project, $tag]) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('{{ __('messages.delete_tag_confirm', ['tag' => $tag->label]) }}')"
                                                class="opacity-60 hover:opacity-100 transition-opacity leading-none"
                                                style="color: {{ $tag->color }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        </button>
                                    </form>
                                </span>
                            @endforeach
                        </div>
                    @endif
                    <form method="POST" action="{{ route('projects.pull_tags.store', $project) }}" class="flex items-end gap-3">
                        @csrf
                        <div class="flex-1">
                            <input
                                type="text"
                                name="label"
                                placeholder="{{ __('messages.tag_name_ph') }}"
                                value="{{ old('label') }}"
                                class="w-full bg-white/4 border border-white/10 rounded-lg px-3 py-2 text-sm text-white/80 placeholder-white/20 focus:outline-none focus:border-white/25"
                            >
                            @error('label')
                                <p class="text-red-400/80 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <input
                                type="color"
                                name="color"
                                value="{{ old('color', '#6366f1') }}"
                                class="h-9 w-14 bg-white/4 border border-white/10 rounded-lg cursor-pointer"
                            >
                        </div>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white/70 bg-white/6 border border-white/15 rounded-lg hover:bg-white/10 hover:border-white/25 transition-colors whitespace-nowrap">
                            {{ __('messages.create_tag_btn') }}
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- Lista pull request --}}
        <div class="border border-white/10 rounded-xl overflow-hidden">
            <div class="px-4 py-2.5 border-b border-white/8 bg-white/2 flex items-center justify-between">
                <span class="text-white/40 text-xs font-medium uppercase tracking-wider">{{ __('messages.pull_open_header') }}</span>
                <span class="text-white/20 text-xs">{{ $pullRequests->count() }} PR</span>
            </div>

            @if ($pullRequests->isEmpty())
                <div class="px-5 py-12 text-center text-white/25 text-sm">
                    {{ __('messages.no_pulls_open') }}
                </div>
            @else
                <div class="divide-y divide-white/6">
                    @foreach ($pullRequests as $pr)
                        <a href="{{ route('projects.pull.show', [$project, $pr]) }}"
                           class="flex items-center gap-4 px-5 py-4 hover:bg-white/3 transition-colors group no-underline">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                @if ($pr->isOpen())
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" style="color: rgb(52 211 153 / 0.7)" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9z" clip-rule="evenodd"/>
                                    </svg>
                                @elseif ($pr->isAccepted())
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" style="color: rgb(99 102 241 / 0.7)" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" style="color: rgb(248 113 113 / 0.5)" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium truncate" style="color: rgb(255 255 255 / 0.7)">
                                        {{ $pr->title }}
                                    </p>
                                    <p class="text-xs mt-0.5 truncate" style="color: rgb(255 255 255 / 0.25)">
                                        #{{ $pr->id }} · {{ $pr->isOpen() ? __('messages.status_open_pr') : ($pr->isAccepted() ? __('messages.status_accepted') : __('messages.status_rejected')) }} {{ __('messages.from') }} {{ $pr->user->name }} · {{ $pr->created_at->diffForHumans() }}
                                        @if ($pr->sourceProject)
                                            · {{ __('messages.from') }} <span class="text-white/35">{{ $pr->sourceProject->name }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                @if ($pr->tags->isNotEmpty())
                                    <div class="flex items-center gap-1.5">
                                        @foreach ($pr->tags as $tag)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                                  style="background-color: {{ $tag->color }}22; color: {{ $tag->color }}; border: 1px solid {{ $tag->color }}44;">
                                                {{ $tag->label }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                                @if ($pr->comments_count > 0)
                                    <div class="flex items-center gap-1 text-xs" style="color: rgb(255 255 255 / 0.25)">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 2c-2.236 0-4.43.18-6.57.524C1.993 2.755 1 4.014 1 5.426v5.148c0 1.413.993 2.67 2.43 2.902.848.137 1.705.248 2.57.331v3.443a.75.75 0 0 0 1.28.53l3.58-3.579a.78.78 0 0 1 .527-.224 41.202 41.202 0 0 0 5.183-.5c1.437-.232 2.43-1.49 2.43-2.903V5.426c0-1.413-.993-2.67-2.43-2.902A41.289 41.289 0 0 0 10 2zm0 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2zM8 9a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm5 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $pr->comments_count }}</span>
                                    </div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

    </main>

</body>
</html>
