<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pullRequest->title }} · Merge · {{ $project->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0d0d0d] min-h-screen flex flex-col">

    <header class="flex items-center justify-between px-6 py-3 border-b border-white/10">
        <a href="{{ route('projects.merge', $project) }}" class="text-white/60 text-sm font-medium tracking-wide hover:text-white/80 transition-colors">
            {{ __('messages.nav_merge') }}
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

        {{-- Intestazione merge --}}
        <div class="mb-6">
            <div class="flex items-start gap-3 mb-3">
                <div class="mt-1 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: rgb(129 140 248 / 0.8)">
                        <circle cx="18" cy="18" r="3"/><circle cx="6" cy="6" r="3"/><path d="M6 21V9a9 9 0 0 0 9 9"/>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <h1 class="text-xl font-bold text-white/85 leading-snug">{{ $pullRequest->title }}</h1>
                    <div class="flex items-center gap-2 mt-2 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-500/15 text-indigo-400/80 border border-indigo-500/20">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="18" cy="18" r="3"/><circle cx="6" cy="6" r="3"/><path d="M6 21V9a9 9 0 0 0 9 9"/>
                            </svg>
                            {{ __('messages.merged_badge') }}
                        </span>
                        <span class="text-white/25 text-xs">
                            #{{ $pullRequest->id }} · {{ __('messages.merged_by_label') }} <a href="{{ route('users.show', $pullRequest->user) }}" class="text-white/40 hover:text-white/60 transition-colors">{{ $pullRequest->user->name }}</a> · {{ $pullRequest->updated_at->diffForHumans() }}
                        </span>
                        @if ($pullRequest->sourceProject)
                            <span class="text-white/20 text-xs">
                                · {{ __('messages.from') }} <a href="{{ route('projects.show', $pullRequest->sourceProject) }}" class="text-white/40 hover:text-white/60 transition-colors">{{ $pullRequest->sourceProject->name }}</a>
                            </span>
                        @endif
                        @foreach ($pullRequest->tags as $tag)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                  style="background-color: {{ $tag->color }}22; color: {{ $tag->color }}; border: 1px solid {{ $tag->color }}44;">
                                {{ $tag->label }}
                            </span>
                        @endforeach
                    </div>
                </div>

                {{-- Azioni --}}
                <div class="flex items-center gap-2 shrink-0">
                    @if ($pullRequest->sourceProject)
                        <a href="{{ route('projects.merge.diff', [$project, $pullRequest]) }}"
                           class="px-3 py-1.5 text-xs font-medium text-indigo-300/70 bg-indigo-500/10 border border-indigo-500/20 rounded-lg hover:bg-indigo-500/20 hover:border-indigo-500/35 hover:text-indigo-300 transition-colors">
                            {{ __('messages.compare_files') }}
                        </a>
                    @endif
                    <a href="{{ route('projects.pull.show', [$project, $pullRequest]) }}"
                       class="px-3 py-1.5 text-xs font-medium text-white/35 border border-white/10 rounded-lg hover:text-white/60 hover:border-white/25 transition-colors">
                        {{ __('messages.view_pr') }}
                    </a>
                </div>
            </div>
        </div>

        {{-- Descrizione --}}
        <div class="mb-6 border border-white/10 rounded-xl overflow-hidden">
            <div class="px-4 py-2.5 border-b border-white/8 bg-white/2 flex items-center gap-2">
                <a href="{{ route('users.show', $pullRequest->user) }}" class="text-sm font-medium text-white/60 hover:text-white/80 transition-colors">{{ $pullRequest->user->name }}</a>
                <span class="text-white/25 text-xs">{{ $pullRequest->created_at->diffForHumans() }}</span>
                @if ($pullRequest->updated_at->ne($pullRequest->created_at))
                    <span class="text-white/20 text-xs">{{ __('messages.edited') }}</span>
                @endif
            </div>
            <div class="p-5">
                @if ($pullRequest->body)
                    <div class="md-rendered text-sm text-white/65 leading-relaxed" data-markdown="{{ $pullRequest->body }}"></div>
                @else
                    <p class="text-white/25 text-sm italic">{{ __('messages.no_description') }}</p>
                @endif
            </div>
        </div>

        {{-- Commenti --}}
        @if ($pullRequest->comments->isNotEmpty())
            <div class="border border-white/10 rounded-xl overflow-hidden mb-6">
                <div class="px-4 py-2.5 border-b border-white/8 bg-white/2 flex items-center justify-between">
                    <span class="text-white/40 text-xs font-medium uppercase tracking-wider">{{ __('messages.comments_header') }}</span>
                    <span class="text-white/20 text-xs">{{ $pullRequest->comments->count() }} {{ $pullRequest->comments->count() === 1 ? __('messages.comment_singular') : __('messages.comment_plural') }}</span>
                </div>
                <div class="divide-y divide-white/6">
                    @foreach ($pullRequest->comments as $comment)
                        <div class="p-5" id="comment-{{ $comment->id }}">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('users.show', $comment->user) }}" class="text-sm font-medium text-white/60 hover:text-white/80 transition-colors">{{ $comment->user->name }}</a>
                                    <span class="text-white/25 text-xs">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                @if ($comment->user_id === Auth::id() || $project->isAdminFor(Auth::user()))
                                    <form method="POST"
                                          action="{{ route('projects.pull.comments.destroy', [$project, $pullRequest, $comment]) }}"
                                          onsubmit="return confirm('{{ __('messages.delete_comment_confirm') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-400/40 hover:text-red-400/70 transition-colors">
                                            {{ __('messages.delete') }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                            <div class="md-rendered text-sm text-white/65 leading-relaxed" data-markdown="{{ $comment->body }}"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Nuovo commento --}}
        <div class="border border-white/10 rounded-xl overflow-hidden">
            <div class="px-4 py-2.5 border-b border-white/8 bg-white/2">
                <span class="text-white/40 text-xs font-medium uppercase tracking-wider">{{ __('messages.add_comment') }}</span>
            </div>
            <div class="p-4">
                <form method="POST" action="{{ route('projects.pull.comments.store', [$project, $pullRequest]) }}">
                    @csrf
                    @include('user.partials.md_editor', [
                        'name' => 'body',
                        'value' => old('body', ''),
                        'rows' => 5,
                        'placeholder' => __('messages.write_comment_ph'),
                        'size' => 'sm',
                    ])
                    @error('body')
                        <p class="text-red-400/80 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <div class="flex justify-end mt-3">
                        <button type="submit"
                            class="px-4 py-1.5 text-sm font-medium text-white/70 bg-white/6 border border-white/15 rounded-lg hover:bg-white/10 hover:border-white/25 transition-colors">
                            {{ __('messages.comment_btn') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </main>

</body>
</html>
