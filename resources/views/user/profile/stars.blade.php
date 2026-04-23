<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.my_stars_title') }}</title>
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

        <h1 class="text-2xl font-bold text-white/85 mb-8">{{ __('messages.my_stars_title') }}</h1>

        @if ($projects->isEmpty())
            <div class="text-center py-20 text-white/30 text-sm">
                {{ __('messages.no_stars_yet') }}
            </div>
        @else
            <div class="flex flex-col gap-3">
                @foreach ($projects as $project)
                    <div class="flex items-start gap-3 px-5 py-4 border border-white/10 rounded-xl hover:border-white/20 hover:bg-white/3 transition-all group">
                        <a href="{{ route('projects.show', $project) }}" class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-white/85 font-medium group-hover:text-white transition-colors">{{ $project->name }}</span>
                                <span class="text-xs text-white/25 border border-white/10 rounded px-1.5 py-0.5">{{ $project->visibility->label() }}</span>
                            </div>
                            @if ($project->description)
                                <p class="text-white/35 text-sm mt-0.5 truncate">{{ $project->description }}</p>
                            @endif
                            <div class="flex items-center gap-3 mt-1.5 text-xs text-white/20">
                                <span>{{ $project->user->displayName() }}</span>
                                <span>·</span>
                                <span>★ {{ $project->stars_count }}</span>
                            </div>
                        </a>

                        <form method="POST" action="{{ route('projects.star', $project) }}" class="shrink-0 self-center">
                            @csrf
                            <button type="submit" class="px-2.5 py-1 text-sm rounded-md border border-yellow-400/30 text-yellow-400 hover:border-yellow-400/50 transition-colors">
                                ★
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </main>

</body>
</html>
