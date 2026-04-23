<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $profileUser->displayName() }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0d0d0d] min-h-screen flex flex-col">

    <header class="flex items-center justify-between px-6 py-3 border-b border-white/10">
        @auth
            <a href="{{ route('projects.index') }}" class="text-white/60 text-sm font-medium tracking-wide hover:text-white/80 transition-colors">
                {{ __('messages.nav_laws') }}
            </a>
            <div class="flex items-center gap-3">
                @if (Auth::id() === $profileUser->id)
                    <a href="{{ route('profile.edit') }}" class="text-white/40 text-sm hover:text-white/60 transition-colors">
                        {{ __('messages.edit_profile_link') }}
                    </a>
                @else
                    <a href="{{ route('users.show', Auth::user()) }}" class="text-white/40 text-sm hover:text-white/60 transition-colors">
                        {{ Auth::user()->displayName() }}
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-4 py-1.5 text-sm font-medium text-white/60 border border-white/15 rounded-lg hover:border-white/30 hover:text-white/80 transition-colors">
                        {{ __('messages.logout') }}
                    </button>
                </form>
            </div>
        @else
            <a href="{{ route('home') }}" class="text-white/60 text-sm font-medium tracking-wide hover:text-white/80 transition-colors">
                {{ __('messages.nav_home') }}
            </a>
            <a href="{{ route('login') }}" class="px-4 py-1.5 text-sm font-medium text-white/60 border border-white/15 rounded-lg hover:border-white/30 hover:text-white/80 transition-colors">
                {{ __('messages.login_link') }}
            </a>
        @endauth
    </header>

    <main class="flex-1 px-6 py-10 max-w-4xl mx-auto w-full">

        {{-- Intestazione profilo --}}
        <div class="mb-10">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-white/8 border border-white/10 flex items-center justify-center text-white/40 text-xl font-bold select-none">
                    {{ mb_strtoupper(mb_substr($profileUser->displayName(), 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white/85">
                        {{ $profileUser->nickname ? '@'.$profileUser->nickname : $profileUser->name }}
                    </h1>
                    @if ($profileUser->nickname && $profileUser->show_name)
                        <p class="text-white/35 text-sm mt-0.5">{{ $profileUser->name }}</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-4 mt-4 text-xs text-white/25">
                <span>{{ $projects->count() }} {{ $projects->count() === 1 ? __('messages.law_singular') : __('messages.law_plural') }}</span>
                <span>·</span>
                <span>★ {{ $starredProjects->count() }} {{ $starredProjects->count() === 1 ? __('messages.star_singular') : __('messages.star_plural') }}</span>
            </div>
        </div>

        {{-- Navigazione profilo --}}
        <nav class="flex items-center gap-1 mb-6 border-b border-white/8 pb-0">
            <button
                onclick="showTab('projects')"
                id="tab-projects-btn"
                class="px-3 py-2 text-sm font-medium text-white/80 border-b-2 border-white/60 -mb-px transition-colors">
                {{ __('messages.tab_laws') }}
            </button>
            <button
                onclick="showTab('stars')"
                id="tab-stars-btn"
                class="px-3 py-2 text-sm font-medium text-white/35 border-b-2 border-transparent -mb-px hover:text-white/60 transition-colors">
                {{ __('messages.tab_stars') }}
            </button>
        </nav>

        {{-- Sezione Leggi --}}
        <div id="tab-projects">
            @if ($projects->isEmpty())
                <div class="text-center py-16 text-white/30 text-sm">
                    {{ __('messages.no_public_laws') }}
                </div>
            @else
                <div class="flex flex-col gap-3">
                    @foreach ($projects as $project)
                        <a href="{{ route('projects.show', $project) }}"
                            class="flex items-start justify-between px-5 py-4 border border-white/10 rounded-xl hover:border-white/20 hover:bg-white/3 transition-all group">
                            <div class="flex flex-col gap-1 min-w-0">
                                <span class="text-white/85 font-medium group-hover:text-white transition-colors">{{ $project->name }}</span>
                                @if ($project->description)
                                    <span class="text-white/35 text-sm truncate">{{ $project->description }}</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 shrink-0 ml-4">
                                @if ($project->stars_count > 0)
                                    <span class="text-white/25 text-xs">★ {{ $project->stars_count }}</span>
                                @endif
                                <span class="text-white/25 text-xs">{{ $project->created_at->diffForHumans() }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Sezione Stelle --}}
        <div id="tab-stars" class="hidden">
            @if ($starredProjects->isEmpty())
                <div class="text-center py-16 text-white/30 text-sm">
                    {{ __('messages.no_starred_laws') }}
                </div>
            @else
                <div class="flex flex-col gap-3">
                    @foreach ($starredProjects as $project)
                        <a href="{{ route('projects.show', $project) }}"
                            class="flex items-start justify-between px-5 py-4 border border-white/10 rounded-xl hover:border-white/20 hover:bg-white/3 transition-all group">
                            <div class="flex flex-col gap-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-white/85 font-medium group-hover:text-white transition-colors">{{ $project->name }}</span>
                                    <span class="text-white/20 text-xs">{{ $project->user->displayName() }}</span>
                                </div>
                                @if ($project->description)
                                    <span class="text-white/35 text-sm truncate">{{ $project->description }}</span>
                                @endif
                            </div>
                            @if ($project->stars_count > 0)
                                <span class="text-white/25 text-xs shrink-0 ml-4">★ {{ $project->stars_count }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

    </main>

    <script>
        function showTab(name) {
            document.getElementById('tab-projects').classList.add('hidden');
            document.getElementById('tab-stars').classList.add('hidden');
            document.getElementById('tab-projects-btn').className = 'px-3 py-2 text-sm font-medium text-white/35 border-b-2 border-transparent -mb-px hover:text-white/60 transition-colors';
            document.getElementById('tab-stars-btn').className = 'px-3 py-2 text-sm font-medium text-white/35 border-b-2 border-transparent -mb-px hover:text-white/60 transition-colors';

            document.getElementById('tab-' + name).classList.remove('hidden');
            document.getElementById('tab-' + name + '-btn').className = 'px-3 py-2 text-sm font-medium text-white/80 border-b-2 border-white/60 -mb-px transition-colors';
        }
    </script>

</body>
</html>
