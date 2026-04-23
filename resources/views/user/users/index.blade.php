<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.users_title') }}</title>
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

        <h1 class="text-2xl font-bold text-white/85 mb-6">{{ __('messages.users_title') }}</h1>

        {{-- Ricerca --}}
        <form method="GET" action="{{ route('users.index') }}" class="mb-6">
            <div class="flex gap-2">
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="{{ __('messages.search_users_ph') }}"
                    class="flex-1 bg-white/5 border border-white/10 rounded-lg px-3.5 py-2 text-sm text-white/85 placeholder-white/20 focus:outline-none focus:border-white/30 transition-colors"
                    autofocus
                />
                <button type="submit" class="px-4 py-2 bg-white/8 border border-white/10 rounded-lg text-sm text-white/60 hover:bg-white/12 hover:text-white/80 transition-colors">
                    {{ __('messages.search') }}
                </button>
            </div>
        </form>

        @if ($users->isEmpty())
            <div class="text-center py-20 text-white/30 text-sm">
                {{ __('messages.no_users_found') }}
            </div>
        @else
            <div class="flex flex-col gap-2">
                @foreach ($users as $user)
                    <a href="{{ route('users.show', $user) }}"
                        class="flex items-center gap-4 px-5 py-3.5 border border-white/10 rounded-xl hover:border-white/20 hover:bg-white/3 transition-all group">
                        <div class="w-9 h-9 rounded-full bg-white/8 border border-white/10 flex items-center justify-center text-white/40 text-sm font-bold shrink-0 select-none">
                            {{ mb_strtoupper(mb_substr($user->displayName(), 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-white/85 font-medium group-hover:text-white transition-colors">
                                    {{ $user->nickname ? '@'.$user->nickname : $user->name }}
                                </span>
                                @if ($user->nickname && $user->show_name)
                                    <span class="text-white/30 text-sm">{{ $user->name }}</span>
                                @endif
                            </div>
                        </div>
                        <span class="text-white/20 text-xs shrink-0">{{ $user->projects_count }} {{ $user->projects_count === 1 ? __('messages.law_singular') : __('messages.law_plural') }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </main>

</body>
</html>
