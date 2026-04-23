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
        <a href="{{ route('projects.opinions', $project) }}" class="text-white/60 text-sm font-medium tracking-wide hover:text-white/80 transition-colors">
            {{ __('messages.nav_opinions') }}
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

        @php
            $upvotes   = $opinion->votes->where('value', 1)->count();
            $downvotes = $opinion->votes->where('value', -1)->count();
            $myVote    = $opinion->votes->firstWhere('user_id', Auth::id())?->value;
        @endphp

        {{-- Opinion header --}}
        <div class="mb-6">
            <div class="flex items-start justify-between gap-4 mb-3">
                <div class="flex items-center gap-2 flex-wrap">
                    <a href="{{ route('users.show', $opinion->user) }}" class="text-sm font-medium text-white/70 hover:text-white/90 transition-colors">{{ $opinion->user->name }}</a>
                    <span class="text-white/25 text-xs">{{ $opinion->created_at->diffForHumans() }}</span>
                    @if ($opinion->updated_at->ne($opinion->created_at))
                        <span class="text-white/20 text-xs">{{ __('messages.edited') }}</span>
                    @endif
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    {{-- Vote buttons --}}
                    <div class="flex items-center gap-1">
                        <form method="POST" action="{{ route('projects.opinions.vote', [$project, $opinion]) }}">
                            @csrf
                            <input type="hidden" name="value" value="up">
                            <button type="submit"
                                class="flex items-center gap-1 px-2 py-0.5 rounded text-xs transition-colors {{ $myVote === 1 ? 'text-emerald-400/80 bg-emerald-400/10' : 'text-white/25 hover:text-white/50' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 17a.75.75 0 0 1-.75-.75V5.612L5.29 9.77a.75.75 0 0 1-1.08-1.04l5.25-5.5a.75.75 0 0 1 1.08 0l5.25 5.5a.75.75 0 1 1-1.08 1.04L10.75 5.612V16.25A.75.75 0 0 1 10 17Z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ $upvotes }}</span>
                            </button>
                        </form>
                        <form method="POST" action="{{ route('projects.opinions.vote', [$project, $opinion]) }}">
                            @csrf
                            <input type="hidden" name="value" value="down">
                            <button type="submit"
                                class="flex items-center gap-1 px-2 py-0.5 rounded text-xs transition-colors {{ $myVote === -1 ? 'text-rose-400/80 bg-rose-400/10' : 'text-white/25 hover:text-white/50' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a.75.75 0 0 1 .75.75v10.638l3.96-4.158a.75.75 0 1 1 1.08 1.04l-5.25 5.5a.75.75 0 0 1-1.08 0l-5.25-5.5a.75.75 0 1 1 1.08-1.04l3.96 4.158V3.75A.75.75 0 0 1 10 3Z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ $downvotes }}</span>
                            </button>
                        </form>
                    </div>
                    @if ($opinion->user_id === Auth::id() || $project->isAdminFor(Auth::user()))
                        <form method="POST" action="{{ route('projects.opinions.destroy', [$project, $opinion]) }}"
                              onsubmit="return confirm('{{ __('messages.delete_opinion_confirm') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-red-400/40 hover:text-red-400/70 transition-colors">
                                {{ __('messages.delete') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Opinion body --}}
        <div class="mb-6 border border-white/10 rounded-xl overflow-hidden">
            <div class="px-4 py-2.5 border-b border-white/8 bg-white/2 flex items-center justify-between">
                <span class="text-white/40 text-xs font-medium uppercase tracking-wider">{{ __('messages.tab_opinions') }}</span>
                @if ($opinion->user_id === Auth::id())
                    <button type="button"
                        onclick="toggleEdit('opinion-edit-form')"
                        class="text-xs text-white/30 hover:text-white/60 transition-colors">
                        {{ __('messages.edit') }}
                    </button>
                @endif
            </div>
            <div class="p-5">
                <div class="md-rendered text-sm text-white/65 leading-relaxed" data-markdown="{{ $opinion->body }}"></div>
            </div>
            @if ($opinion->user_id === Auth::id())
                <div id="opinion-edit-form" class="hidden px-5 pb-5">
                    <form method="POST" action="{{ route('projects.opinions.update', [$project, $opinion]) }}">
                        @csrf
                        @method('PATCH')
                        @include('user.partials.md_editor', [
                            'name' => 'body',
                            'value' => $opinion->body,
                            'rows' => 6,
                            'placeholder' => '',
                            'size' => 'sm',
                        ])
                        <div class="flex gap-2 justify-end mt-2">
                            <button type="button"
                                onclick="toggleEdit('opinion-edit-form')"
                                class="px-3 py-1 text-xs text-white/40 hover:text-white/60 transition-colors">
                                {{ __('messages.cancel') }}
                            </button>
                            <button type="submit"
                                class="px-3 py-1 text-xs font-medium text-white/70 bg-white/6 border border-white/15 rounded-lg hover:bg-white/10 hover:border-white/25 transition-colors">
                                {{ __('messages.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        {{-- Replies --}}
        @if ($opinion->replies->isNotEmpty())
            <div class="border border-white/10 rounded-xl overflow-hidden mb-6">
                <div class="px-4 py-2.5 border-b border-white/8 bg-white/2 flex items-center justify-between">
                    <span class="text-white/40 text-xs font-medium uppercase tracking-wider">{{ __('messages.comments_header') }}</span>
                    <span class="text-white/20 text-xs">{{ $opinion->replies->count() }} {{ $opinion->replies->count() === 1 ? __('messages.comment_singular') : __('messages.comment_plural') }}</span>
                </div>
                <div class="divide-y divide-white/6">
                    @foreach ($opinion->replies as $reply)
                        <div class="p-5" id="reply-{{ $reply->id }}">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('users.show', $reply->user) }}" class="text-sm font-medium text-white/60 hover:text-white/80 transition-colors">{{ $reply->user->name }}</a>
                                    <span class="text-white/25 text-xs">{{ $reply->created_at->diffForHumans() }}</span>
                                    @if ($reply->updated_at->ne($reply->created_at))
                                        <span class="text-white/20 text-xs">{{ __('messages.edited') }}</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    @if ($reply->user_id === Auth::id())
                                        <button type="button"
                                            onclick="toggleEdit('reply-edit-{{ $reply->id }}')"
                                            class="text-xs text-white/30 hover:text-white/60 transition-colors">
                                            {{ __('messages.edit') }}
                                        </button>
                                    @endif
                                    @if ($reply->user_id === Auth::id() || $project->isAdminFor(Auth::user()))
                                        <form method="POST" action="{{ route('projects.opinions.replies.destroy', [$project, $opinion, $reply]) }}"
                                              onsubmit="return confirm('{{ __('messages.delete_reply_confirm') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-400/40 hover:text-red-400/70 transition-colors">
                                                {{ __('messages.delete') }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                            <div class="md-rendered text-sm text-white/65 leading-relaxed" data-markdown="{{ $reply->body }}"></div>
                            @if ($reply->user_id === Auth::id())
                                <div id="reply-edit-{{ $reply->id }}" class="hidden mt-3">
                                    <form method="POST" action="{{ route('projects.opinions.replies.update', [$project, $opinion, $reply]) }}">
                                        @csrf
                                        @method('PATCH')
                                        @include('user.partials.md_editor', [
                                            'name' => 'body',
                                            'value' => $reply->body,
                                            'rows' => 4,
                                            'placeholder' => '',
                                            'size' => 'sm',
                                        ])
                                        <div class="flex gap-2 justify-end mt-2">
                                            <button type="button"
                                                onclick="toggleEdit('reply-edit-{{ $reply->id }}')"
                                                class="px-3 py-1 text-xs text-white/40 hover:text-white/60 transition-colors">
                                                {{ __('messages.cancel') }}
                                            </button>
                                            <button type="submit"
                                                class="px-3 py-1 text-xs font-medium text-white/70 bg-white/6 border border-white/15 rounded-lg hover:bg-white/10 hover:border-white/25 transition-colors">
                                                {{ __('messages.save') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- New reply form --}}
        <div class="border border-white/10 rounded-xl overflow-hidden">
            <div class="px-4 py-2.5 border-b border-white/8 bg-white/2">
                <span class="text-white/40 text-xs font-medium uppercase tracking-wider">{{ __('messages.add_comment') }}</span>
            </div>
            <div class="p-4">
                <form method="POST" action="{{ route('projects.opinions.replies.store', [$project, $opinion]) }}">
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
                            {{ __('messages.publish_reply_btn') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </main>

</body>
</html>
