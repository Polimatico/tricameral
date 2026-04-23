<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.tab_settings') }} · {{ $project->name }}</title>
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
               class="px-3 py-2 text-sm font-medium text-white/80 border-b-2 border-white/60 -mb-px transition-colors">
                {{ __('messages.tab_settings') }}
            </a>
        </nav>

        @if (session('success'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-white/5 border border-white/10 text-white/60 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="space-y-4">

            {{-- Generale --}}
            <div class="border border-white/10 rounded-xl overflow-hidden">
                <div class="px-4 py-2.5 border-b border-white/8 bg-white/2">
                    <span class="text-white/40 text-xs font-medium uppercase tracking-wider">{{ __('messages.settings_general') }}</span>
                </div>
                <form method="POST" action="{{ route('projects.settings.update', $project) }}" class="px-5 py-4 space-y-4">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="visibility" value="{{ $project->visibility->value }}">
                    <input type="hidden" name="fork_permission" value="{{ $project->fork_permission->value }}">
                    <input type="hidden" name="pull_permission" value="{{ $project->pull_permission->value }}">
                    <input type="hidden" name="pull_visibility" value="{{ $project->pull_visibility->value }}">

                    <div>
                        <label for="name" class="block text-white/50 text-xs mb-1.5">{{ __('messages.settings_law_name') }}</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $project->name) }}"
                            class="w-full bg-white/4 border border-white/10 rounded-lg px-3 py-2 text-white/80 text-sm placeholder-white/20 focus:outline-none focus:border-white/25 transition-colors @error('name') border-red-500/40 @enderror"
                        >
                        @error('name')
                            <p class="mt-1 text-xs text-red-400/70">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-white/50 text-xs mb-1.5">{{ __('messages.description') }}</label>
                        <textarea
                            id="description"
                            name="description"
                            rows="3"
                            class="w-full bg-white/4 border border-white/10 rounded-lg px-3 py-2 text-white/80 text-sm placeholder-white/20 focus:outline-none focus:border-white/25 transition-colors resize-none @error('description') border-red-500/40 @enderror"
                            placeholder="{{ __('messages.settings_desc_ph') }}"
                        >{{ old('description', $project->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-red-400/70">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-1.5 text-sm font-medium text-white/70 border border-white/15 rounded-lg hover:border-white/30 hover:text-white/90 transition-colors">
                            {{ __('messages.save') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Visibilità --}}
            <div class="border border-white/10 rounded-xl overflow-hidden">
                <div class="px-4 py-2.5 border-b border-white/8 bg-white/2">
                    <span class="text-white/40 text-xs font-medium uppercase tracking-wider">{{ __('messages.settings_visibility') }}</span>
                </div>
                <form method="POST" action="{{ route('projects.settings.update', $project) }}" class="px-5 py-4 space-y-3">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="name" value="{{ $project->name }}">
                    <input type="hidden" name="description" value="{{ $project->description }}">
                    <input type="hidden" name="fork_permission" value="{{ $project->fork_permission->value }}">
                    <input type="hidden" name="pull_permission" value="{{ $project->pull_permission->value }}">
                    <input type="hidden" name="pull_visibility" value="{{ $project->pull_visibility->value }}">

                    @foreach (\App\Enums\ProjectVisibility::cases() as $visibility)
                        <label class="flex items-start gap-3 cursor-pointer group">
                            <input
                                type="radio"
                                name="visibility"
                                value="{{ $visibility->value }}"
                                {{ $project->visibility === $visibility ? 'checked' : '' }}
                                class="mt-0.5 accent-white/60"
                            >
                            <div>
                                <p class="text-white/70 text-sm group-hover:text-white/90 transition-colors">{{ $visibility->label() }}</p>
                                <p class="text-white/30 text-xs">{{ $visibility->description() }}</p>
                            </div>
                        </label>
                    @endforeach

                    @error('visibility')
                        <p class="text-xs text-red-400/70">{{ $message }}</p>
                    @enderror

                    <div class="flex justify-end pt-1">
                        <button type="submit" class="px-4 py-1.5 text-sm font-medium text-white/70 border border-white/15 rounded-lg hover:border-white/30 hover:text-white/90 transition-colors">
                            {{ __('messages.save') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Fork --}}
            <div class="border border-white/10 rounded-xl overflow-hidden">
                <div class="px-4 py-2.5 border-b border-white/8 bg-white/2">
                    <span class="text-white/40 text-xs font-medium uppercase tracking-wider">{{ __('messages.settings_fork') }}</span>
                </div>
                <form method="POST" action="{{ route('projects.settings.update', $project) }}" class="px-5 py-4 space-y-3">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="name" value="{{ $project->name }}">
                    <input type="hidden" name="description" value="{{ $project->description }}">
                    <input type="hidden" name="visibility" value="{{ $project->visibility->value }}">
                    <input type="hidden" name="pull_permission" value="{{ $project->pull_permission->value }}">
                    <input type="hidden" name="pull_visibility" value="{{ $project->pull_visibility->value }}">

                    @foreach (\App\Enums\ForkPermission::cases() as $permission)
                        <label class="flex items-start gap-3 cursor-pointer group">
                            <input
                                type="radio"
                                name="fork_permission"
                                value="{{ $permission->value }}"
                                {{ $project->fork_permission === $permission ? 'checked' : '' }}
                                class="mt-0.5 accent-white/60"
                            >
                            <div>
                                <p class="text-white/70 text-sm group-hover:text-white/90 transition-colors">{{ $permission->label() }}</p>
                                <p class="text-white/30 text-xs">{{ $permission->description() }}</p>
                            </div>
                        </label>
                    @endforeach

                    @error('fork_permission')
                        <p class="text-xs text-red-400/70">{{ $message }}</p>
                    @enderror

                    <div class="flex justify-end pt-1">
                        <button type="submit" class="px-4 py-1.5 text-sm font-medium text-white/70 border border-white/15 rounded-lg hover:border-white/30 hover:text-white/90 transition-colors">
                            {{ __('messages.save') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Visibilità fork --}}
            <div class="border border-white/10 rounded-xl overflow-hidden">
                <div class="px-4 py-2.5 border-b border-white/8 bg-white/2">
                    <span class="text-white/40 text-xs font-medium uppercase tracking-wider">{{ __('messages.settings_fork_visibility') }}</span>
                </div>
                <form method="POST" action="{{ route('projects.fork.listing', $project) }}" class="px-5 py-4 space-y-3">
                    @csrf
                    @method('PATCH')
                    @foreach (\App\Enums\ForkListing::cases() as $mode)
                        <label class="flex items-start gap-3 cursor-pointer group">
                            <input
                                type="radio"
                                name="fork_listing"
                                value="{{ $mode->value }}"
                                {{ $project->fork_listing === $mode ? 'checked' : '' }}
                                class="mt-0.5 accent-white/60"
                            >
                            <div>
                                <p class="text-white/70 text-sm group-hover:text-white/90 transition-colors">{{ $mode->label() }}</p>
                                <p class="text-white/30 text-xs">{{ $mode->description() }}</p>
                            </div>
                        </label>
                    @endforeach
                    <div class="flex justify-end pt-1">
                        <button type="submit" class="px-4 py-1.5 text-sm font-medium text-white/70 border border-white/15 rounded-lg hover:border-white/30 hover:text-white/90 transition-colors">
                            {{ __('messages.save') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Membri --}}
            <div class="border border-white/10 rounded-xl overflow-hidden">
                <div class="px-4 py-2.5 border-b border-white/8 bg-white/2">
                    <span class="text-white/40 text-xs font-medium uppercase tracking-wider">{{ __('messages.settings_members') }}</span>
                </div>

                {{-- Aggiungi membro --}}
                <form method="POST" action="{{ route('projects.members.store', $project) }}" class="px-5 py-4 border-b border-white/8">
                    @csrf
                    <p class="text-white/50 text-xs mb-3">{{ __('messages.invite_member') }}</p>
                    <div class="flex gap-2">
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="email@esempio.com"
                            class="flex-1 bg-white/4 border border-white/10 rounded-lg px-3 py-2 text-white/80 text-sm placeholder-white/20 focus:outline-none focus:border-white/25 transition-colors @error('email') border-red-500/40 @enderror"
                        >
                        <select
                            name="role"
                            class="bg-white/4 border border-white/10 rounded-lg px-3 py-2 text-white/60 text-sm focus:outline-none focus:border-white/25 transition-colors"
                        >
                            @foreach (\App\Enums\ProjectRole::cases() as $role)
                                <option value="{{ $role->value }}" {{ old('role') === $role->value ? 'selected' : '' }}>
                                    {{ $role->label() }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white/70 border border-white/15 rounded-lg hover:border-white/30 hover:text-white/90 transition-colors shrink-0">
                            {{ __('messages.add') }}
                        </button>
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-400/70">{{ $message }}</p>
                    @enderror
                </form>

                {{-- Lista membri --}}
                <div class="divide-y divide-white/8">
                    {{-- Proprietario --}}
                    <div class="flex items-center justify-between px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-full bg-white/8 flex items-center justify-center text-white/40 text-xs font-medium">
                                {{ mb_substr($project->user->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-white/70 text-sm">{{ $project->user->name }}</p>
                                <p class="text-white/30 text-xs">{{ $project->user->email }}</p>
                            </div>
                        </div>
                        <span class="text-white/25 text-xs border border-white/8 rounded px-2 py-0.5">{{ __('messages.member_owner') }}</span>
                    </div>

                    @forelse ($members as $member)
                        <div class="flex items-center justify-between px-5 py-3 group">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-full bg-white/8 flex items-center justify-center text-white/40 text-xs font-medium">
                                    {{ mb_substr($member->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-white/70 text-sm">{{ $member->user->name }}</p>
                                    <p class="text-white/30 text-xs">{{ $member->user->email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('projects.members.update', [$project, $member->user]) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select
                                        name="role"
                                        onchange="this.form.submit()"
                                        class="bg-white/4 border border-white/10 rounded px-2 py-1 text-white/50 text-xs focus:outline-none focus:border-white/25 transition-colors"
                                    >
                                        @foreach (\App\Enums\ProjectRole::cases() as $role)
                                            <option value="{{ $role->value }}" {{ $member->role === $role ? 'selected' : '' }}>
                                                {{ $role->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                                <form method="POST" action="{{ route('projects.members.destroy', [$project, $member->user]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2 py-1 text-xs text-white/25 hover:text-red-400/70 transition-colors opacity-0 group-hover:opacity-100">
                                        {{ __('messages.remove') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-6 text-center text-white/25 text-sm">
                            {{ __('messages.no_members_yet') }}
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Pull Request - chi può creare --}}
            <div class="border border-white/10 rounded-xl overflow-hidden">
                <div class="px-4 py-2.5 border-b border-white/8 bg-white/2">
                    <span class="text-white/40 text-xs font-medium uppercase tracking-wider">{{ __('messages.pull_who_can_open') }}</span>
                </div>
                <form method="POST" action="{{ route('projects.settings.update', $project) }}" class="px-5 py-4 space-y-3">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="name" value="{{ $project->name }}">
                    <input type="hidden" name="description" value="{{ $project->description }}">
                    <input type="hidden" name="visibility" value="{{ $project->visibility->value }}">
                    <input type="hidden" name="fork_permission" value="{{ $project->fork_permission->value }}">
                    <input type="hidden" name="pull_visibility" value="{{ $project->pull_visibility->value }}">

                    @foreach (\App\Enums\PullPermission::cases() as $permission)
                        <label class="flex items-start gap-3 cursor-pointer group">
                            <input
                                type="radio"
                                name="pull_permission"
                                value="{{ $permission->value }}"
                                {{ $project->pull_permission === $permission ? 'checked' : '' }}
                                class="mt-0.5 accent-white/60"
                            >
                            <div>
                                <p class="text-white/70 text-sm group-hover:text-white/90 transition-colors">{{ $permission->label() }}</p>
                                <p class="text-white/30 text-xs">{{ $permission->description() }}</p>
                            </div>
                        </label>
                    @endforeach

                    @error('pull_permission')
                        <p class="text-xs text-red-400/70">{{ $message }}</p>
                    @enderror

                    <div class="flex justify-end pt-1">
                        <button type="submit" class="px-4 py-1.5 text-sm font-medium text-white/70 border border-white/15 rounded-lg hover:border-white/30 hover:text-white/90 transition-colors">
                            {{ __('messages.save') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Pull Request - visibilità --}}
            <div class="border border-white/10 rounded-xl overflow-hidden">
                <div class="px-4 py-2.5 border-b border-white/8 bg-white/2">
                    <span class="text-white/40 text-xs font-medium uppercase tracking-wider">{{ __('messages.pull_visibility') }}</span>
                </div>
                <form method="POST" action="{{ route('projects.settings.update', $project) }}" class="px-5 py-4 space-y-3">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="name" value="{{ $project->name }}">
                    <input type="hidden" name="description" value="{{ $project->description }}">
                    <input type="hidden" name="visibility" value="{{ $project->visibility->value }}">
                    <input type="hidden" name="fork_permission" value="{{ $project->fork_permission->value }}">
                    <input type="hidden" name="pull_permission" value="{{ $project->pull_permission->value }}">

                    @foreach (\App\Enums\PullVisibility::cases() as $visibility)
                        <label class="flex items-start gap-3 cursor-pointer group">
                            <input
                                type="radio"
                                name="pull_visibility"
                                value="{{ $visibility->value }}"
                                {{ $project->pull_visibility === $visibility ? 'checked' : '' }}
                                class="mt-0.5 accent-white/60"
                            >
                            <div>
                                <p class="text-white/70 text-sm group-hover:text-white/90 transition-colors">{{ $visibility->label() }}</p>
                                <p class="text-white/30 text-xs">{{ $visibility->description() }}</p>
                            </div>
                        </label>
                    @endforeach

                    @error('pull_visibility')
                        <p class="text-xs text-red-400/70">{{ $message }}</p>
                    @enderror

                    <div class="flex justify-end pt-1">
                        <button type="submit" class="px-4 py-1.5 text-sm font-medium text-white/70 border border-white/15 rounded-lg hover:border-white/30 hover:text-white/90 transition-colors">
                            {{ __('messages.save') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Zona pericolosa --}}
            <div class="border border-red-500/15 rounded-xl overflow-hidden">
                <div class="px-4 py-2.5 border-b border-red-500/10 bg-red-500/3">
                    <span class="text-red-400/50 text-xs font-medium uppercase tracking-wider">{{ __('messages.danger_zone') }}</span>
                </div>
                <div class="px-5 py-4 flex items-center justify-between">
                    <div>
                        <p class="text-white/60 text-sm">{{ __('messages.delete_law') }}</p>
                        <p class="text-white/25 text-xs mt-0.5">{{ __('messages.delete_law_hint') }}</p>
                    </div>
                    <form method="POST" action="{{ route('projects.destroy', $project) }}"
                          onsubmit="return confirm('{{ __('messages.delete_law_confirm') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 text-xs font-medium text-red-400/70 border border-red-500/20 rounded-lg hover:text-red-400 hover:border-red-500/40 transition-colors">
                            {{ __('messages.delete_law') }}
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </main>

</body>
</html>
