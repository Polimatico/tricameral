<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pullRequest->title }} · Pull Request · {{ $project->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0d0d0d] min-h-screen flex flex-col">

    <header class="flex items-center justify-between px-6 py-3 border-b border-white/10">
        <a href="{{ route('projects.pull', $project) }}" class="text-white/60 text-sm font-medium tracking-wide hover:text-white/80 transition-colors">
            {{ __('messages.nav_pull') }}
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

        {{-- Intestazione PR --}}
        <div class="mb-6">
            <div class="flex items-start justify-between gap-4 mb-3">
                <div class="min-w-0">
                    <h1 class="text-xl font-bold text-white/85 leading-snug">{{ $pullRequest->title }}</h1>
                    <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                        @if ($pullRequest->isOpen())
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-500/15 text-emerald-400/80 border border-emerald-500/20">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9z" clip-rule="evenodd"/>
                                </svg>
                                {{ __('messages.pr_open_badge') }}
                            </span>
                        @elseif ($pullRequest->isAccepted())
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-500/15 text-indigo-400/80 border border-indigo-500/20">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/>
                                </svg>
                                {{ __('messages.pr_accepted_badge') }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-red-500/10 text-red-400/70 border border-red-500/15">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z" clip-rule="evenodd"/>
                                </svg>
                                {{ __('messages.pr_rejected_badge') }}
                            </span>
                        @endif
                        <span class="text-white/25 text-xs">
                            #{{ $pullRequest->id }} {{ __('messages.opened_by_pr') }} <a href="{{ route('users.show', $pullRequest->user) }}" class="text-white/40 hover:text-white/60 transition-colors">{{ $pullRequest->user->name }}</a> · {{ $pullRequest->created_at->diffForHumans() }}
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

                @if ($project->isAdminFor(Auth::user()))
                    <div class="flex items-center gap-2 shrink-0 flex-wrap justify-end">
                        @if (! $pullRequest->isAccepted())
                            <form method="POST" action="{{ route('projects.pull.status', [$project, $pullRequest]) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="accepted">
                                <button type="submit"
                                    class="px-3 py-1.5 text-xs font-medium text-indigo-400/70 border border-indigo-500/20 rounded-lg hover:text-indigo-400 hover:border-indigo-500/40 transition-colors">
                                    {{ __('messages.accept_pr') }}
                                </button>
                            </form>
                        @endif
                        @if (! $pullRequest->isRejected())
                            <form method="POST" action="{{ route('projects.pull.status', [$project, $pullRequest]) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit"
                                    class="px-3 py-1.5 text-xs font-medium text-red-400/60 border border-red-500/15 rounded-lg hover:text-red-400/90 hover:border-red-500/35 transition-colors">
                                    {{ __('messages.reject_pr') }}
                                </button>
                            </form>
                        @endif
                        @if (! $pullRequest->isOpen())
                            <form method="POST" action="{{ route('projects.pull.status', [$project, $pullRequest]) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="open">
                                <button type="submit"
                                    class="px-3 py-1.5 text-xs font-medium text-white/40 border border-white/10 rounded-lg hover:text-white/60 hover:border-white/25 transition-colors">
                                    {{ __('messages.reopen_pr') }}
                                </button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('projects.pull.destroy', [$project, $pullRequest]) }}"
                              onsubmit="return confirm('{{ __('messages.delete_pr_confirm') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-red-400/40 hover:text-red-400/70 transition-colors">
                                {{ __('messages.delete') }}
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        {{-- Admin: gestione tag sulla PR --}}
        @if ($project->isAdminFor(Auth::user()))
            <div class="mb-6 border border-white/10 rounded-xl overflow-hidden">
                <div class="px-4 py-2.5 border-b border-white/8 bg-white/2">
                    <span class="text-white/40 text-xs font-medium uppercase tracking-wider">{{ __('messages.pr_tags') }}</span>
                </div>
                <div class="p-4">
                    @if ($pullRequest->tags->isNotEmpty())
                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach ($pullRequest->tags as $tag)
                                <form method="POST" action="{{ route('projects.pull.tags.detach', [$project, $pullRequest, $tag]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium transition-opacity hover:opacity-70"
                                            style="background-color: {{ $tag->color }}22; color: {{ $tag->color }}; border: 1px solid {{ $tag->color }}44;"
                                            title="{{ __('messages.remove') }}">
                                        {{ $tag->label }}
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22z"/>
                                        </svg>
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    @endif

                    @php
                        $assignedTagIds = $pullRequest->tags->pluck('id');
                        $availableTags = $projectTags->reject(fn ($t) => $assignedTagIds->contains($t->id));
                    @endphp
                    @if ($availableTags->isNotEmpty())
                        <form method="POST" action="{{ route('projects.pull.tags.attach', [$project, $pullRequest]) }}" class="flex items-center gap-3">
                            @csrf
                            <select name="tag_id"
                                    class="flex-1 bg-white/4 border border-white/10 rounded-lg px-3 py-2 text-sm text-white/60 focus:outline-none focus:border-white/25">
                                <option value="">{{ __('messages.select_tag') }}</option>
                                @foreach ($availableTags as $tag)
                                    <option value="{{ $tag->id }}">{{ $tag->label }}</option>
                                @endforeach
                            </select>
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white/70 bg-white/6 border border-white/15 rounded-lg hover:bg-white/10 hover:border-white/25 transition-colors whitespace-nowrap">
                                {{ __('messages.add_tag_btn') }}
                            </button>
                        </form>
                    @else
                        <p class="text-white/25 text-xs">
                            @if ($projectTags->isEmpty())
                                {!! __('messages.no_tags_available_pr', ['url' => route('projects.pull', $project)]) !!}
                            @else
                                {{ __('messages.all_tags_assigned') }}
                            @endif
                        </p>
                    @endif
                </div>
            </div>
        @endif

        {{-- Descrizione / Changelog --}}
        <div class="mb-6 border border-white/10 rounded-xl overflow-hidden">
            <div class="px-4 py-2.5 border-b border-white/8 bg-white/2 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <a href="{{ route('users.show', $pullRequest->user) }}" class="text-sm font-medium text-white/60 hover:text-white/80 transition-colors">{{ $pullRequest->user->name }}</a>
                    <span class="text-white/25 text-xs">{{ $pullRequest->created_at->diffForHumans() }}</span>
                    @if ($pullRequest->updated_at->ne($pullRequest->created_at))
                        <span class="text-white/20 text-xs">{{ __('messages.edited') }}</span>
                    @endif
                </div>
                @if ($pullRequest->user_id === Auth::id())
                    <button
                        type="button"
                        onclick="document.getElementById('edit-body-form').classList.toggle('hidden'); document.getElementById('body-preview').classList.toggle('hidden')"
                        class="text-xs text-white/30 hover:text-white/60 transition-colors"
                    >
                        {{ __('messages.edit') }}
                    </button>
                @endif
            </div>
            <div class="p-5">
                <div id="body-preview" class="md-rendered text-sm text-white/65 leading-relaxed" data-markdown="{{ $pullRequest->body }}"></div>

                @if ($pullRequest->user_id === Auth::id())
                    <div id="edit-body-form" class="hidden">
                        <form method="POST" action="{{ route('projects.pull.body', [$project, $pullRequest]) }}">
                            @csrf
                            @method('PATCH')
                            @include('user.partials.md_editor', [
                                'name' => 'body',
                                'value' => old('body', $pullRequest->body),
                                'rows' => 8,
                                'placeholder' => __('messages.pull_body_ph'),
                                'size' => 'sm',
                            ])
                            @error('body')
                                <p class="text-red-400/80 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <div class="flex justify-end gap-2 mt-3">
                                <button type="button"
                                    onclick="document.getElementById('edit-body-form').classList.add('hidden'); document.getElementById('body-preview').classList.remove('hidden')"
                                    class="px-4 py-1.5 text-sm font-medium text-white/40 border border-white/10 rounded-lg hover:border-white/20 hover:text-white/60 transition-colors">
                                    {{ __('messages.cancel') }}
                                </button>
                                <button type="submit"
                                    class="px-4 py-1.5 text-sm font-medium text-white/70 bg-white/6 border border-white/15 rounded-lg hover:bg-white/10 hover:border-white/25 transition-colors">
                                    {{ __('messages.save') }}
                                </button>
                            </div>
                        </form>
                    </div>
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
