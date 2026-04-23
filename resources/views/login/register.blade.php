<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.register_title') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0d0d0d] min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-sm">
        <h1 class="text-2xl font-bold text-white/85 mb-1 text-center">{{ __('messages.register_title') }}</h1>
        <p class="text-white/40 text-sm text-center mb-8">
            {{ __('messages.already_account') }}
            <a href="{{ route('login') }}" class="text-white/60 hover:text-white underline transition-colors">{{ __('messages.login_link') }}</a>
        </p>

        <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-4">
            @csrf

            <div class="flex flex-col gap-1.5">
                <label for="name" class="text-sm text-white/50">{{ __('messages.profile_name') }}</label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autocomplete="name"
                    class="bg-white/5 border border-white/10 rounded-lg px-3.5 py-2.5 text-sm text-white/85 placeholder-white/20 focus:outline-none focus:border-white/30 transition-colors @error('name') border-red-500/50 @enderror"
                    placeholder="{{ __('messages.profile_name') }}"
                />
                @error('name')
                    <p class="text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="nickname" class="text-sm text-white/50">{{ __('messages.profile_nickname') }}</label>
                <input
                    id="nickname"
                    type="text"
                    name="nickname"
                    value="{{ old('nickname') }}"
                    autocomplete="username"
                    class="bg-white/5 border border-white/10 rounded-lg px-3.5 py-2.5 text-sm text-white/85 placeholder-white/20 focus:outline-none focus:border-white/30 transition-colors @error('nickname') border-red-500/50 @enderror"
                    placeholder="{{ __('messages.tuonickname') }}"
                />
                <p class="text-xs text-white/30">{{ __('messages.nickname_hint') }}</p>
                @error('nickname')
                    <p class="text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="email" class="text-sm text-white/50">{{ __('messages.email') }}</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                    class="bg-white/5 border border-white/10 rounded-lg px-3.5 py-2.5 text-sm text-white/85 placeholder-white/20 focus:outline-none focus:border-white/30 transition-colors @error('email') border-red-500/50 @enderror"
                    placeholder="tu@esempio.com"
                />
                @error('email')
                    <p class="text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="password" class="text-sm text-white/50">{{ __('messages.password') }}</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    class="bg-white/5 border border-white/10 rounded-lg px-3.5 py-2.5 text-sm text-white/85 placeholder-white/20 focus:outline-none focus:border-white/30 transition-colors @error('password') border-red-500/50 @enderror"
                    placeholder="{{ __('messages.password_placeholder') }}"
                />
                @error('password')
                    <p class="text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="password_confirmation" class="text-sm text-white/50">{{ __('messages.confirm_password') }}</label>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    class="bg-white/5 border border-white/10 rounded-lg px-3.5 py-2.5 text-sm text-white/85 placeholder-white/20 focus:outline-none focus:border-white/30 transition-colors"
                    placeholder="••••••••"
                />
            </div>

            <button type="submit"
                class="mt-2 w-full py-2.5 bg-white text-black text-sm font-medium rounded-lg hover:bg-white/90 transition-colors">
                {{ __('messages.register_btn') }}
            </button>
        </form>
    </div>

</body>
</html>
