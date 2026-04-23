<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0d0d0d] min-h-screen flex flex-col">

    <header class="flex items-center justify-between px-6 py-3 border-b border-white/10">
        <span class="text-white/60 text-sm font-medium tracking-wide">Tricameral</span>
        <div class="flex items-center gap-3">
            @auth
                <a href="{{ route('users.show', Auth::user()) }}"
                    class="text-white/40 text-sm hover:text-white/60 transition-colors">
                    {{ Auth::user()->displayName() }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="px-4 py-1.5 text-sm font-medium text-white/60 border border-white/15 rounded-lg hover:border-white/30 hover:text-white/80 transition-colors">
                        {{ __('messages.logout') }}
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}"
                    class="px-4 py-1.5 text-sm font-medium text-white/60 border border-white/15 rounded-lg hover:border-white/30 hover:text-white/80 transition-colors">
                    {{ __('messages.login_link') }}
                </a>
            @endauth
        </div>
    </header>

    <main class="flex flex-col items-center justify-center flex-1 px-4 text-center">
        <h1 class="text-4xl font-bold text-white/85 mb-3">{{ __('messages.welcome') }}</h1>
        <p class="text-white/40 text-lg mb-10 max-w-sm">
            @auth
                {!! __('messages.authenticated_as', ['name' => '<span class="text-white/60">'.Auth::user()->displayName().'</span>']) !!}
            @else
                {{ __('messages.create_to_start') }}
            @endauth
        </p>

        @guest
            <a href="{{ route('register') }}"
                class="px-6 py-2.5 bg-white text-black text-sm font-medium rounded-lg hover:bg-white/90 transition-colors">
                {{ __('messages.register_btn_home') }}
            </a>
        @endguest

        @auth
            <a href="{{ route('projects.index') }}"
                class="px-6 py-2.5 bg-white text-black text-sm font-medium rounded-lg hover:bg-white/90 transition-colors">
                {{ __('messages.view_laws') }}
            </a>
        @endauth
    </main>

</body>
</html>
