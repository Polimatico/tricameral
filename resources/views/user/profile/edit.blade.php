<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.profile_title') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0d0d0d] min-h-screen flex flex-col">

    <header class="flex items-center justify-between px-6 py-3 border-b border-white/10">
        <a href="{{ route('home') }}" class="text-white/60 text-sm font-medium tracking-wide hover:text-white/80 transition-colors">
            {{ __('messages.nav_home') }}
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

    <main class="flex flex-col items-center justify-center flex-1 px-4 py-12">
        <div class="w-full max-w-sm">
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-2xl font-bold text-white/85">{{ __('messages.profile_title') }}</h1>
                <a href="{{ route('users.show', Auth::user()) }}" class="text-xs text-white/30 hover:text-white/60 transition-colors">
                    {{ __('messages.view_public_profile') }}
                </a>
            </div>

            @if (session('status'))
                <div class="mb-6 px-4 py-3 rounded-lg bg-green-500/10 border border-green-500/20 text-green-400 text-sm text-center">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}" class="flex flex-col gap-5">
                @csrf
                @method('PATCH')

                {{-- Dati personali --}}
                <div class="flex flex-col gap-4">
                    <p class="text-xs font-medium text-white/30 uppercase tracking-widest">{{ __('messages.profile_info') }}</p>

                    <div class="flex flex-col gap-1.5">
                        <label for="name" class="text-sm text-white/50">{{ __('messages.profile_name') }}</label>
                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            required
                            autocomplete="name"
                            class="bg-white/5 border border-white/10 rounded-lg px-3.5 py-2.5 text-sm text-white/85 placeholder-white/20 focus:outline-none focus:border-white/30 transition-colors @error('name') border-red-500/50 @enderror"
                        />
                        @error('name')
                            <p class="text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label for="nickname" class="text-sm text-white/50">{{ __('messages.profile_nickname') }} <span class="text-white/20">({{ __('messages.optional') }})</span></label>
                        <div class="flex items-center gap-0">
                            <span class="px-3 py-2.5 bg-white/3 border border-r-0 border-white/10 rounded-l-lg text-sm text-white/30">@</span>
                            <input
                                id="nickname"
                                type="text"
                                name="nickname"
                                value="{{ old('nickname', $user->nickname) }}"
                                autocomplete="off"
                                placeholder="{{ __('messages.tuonickname') }}"
                                class="flex-1 bg-white/5 border border-white/10 rounded-r-lg px-3.5 py-2.5 text-sm text-white/85 placeholder-white/20 focus:outline-none focus:border-white/30 transition-colors @error('nickname') border-red-500/50 @enderror"
                            />
                        </div>
                        <p class="text-xs text-white/20">{{ __('messages.nickname_hint') }}</p>
                        @error('nickname')
                            <p class="text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between py-2.5 px-3.5 bg-white/3 border border-white/10 rounded-lg">
                        <div>
                            <p class="text-sm text-white/70">{{ __('messages.show_real_name') }}</p>
                            <p class="text-xs text-white/30 mt-0.5">{{ __('messages.show_name_hint') }}</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer ml-4 shrink-0">
                            <input type="hidden" name="show_name" value="0">
                            <input
                                type="checkbox"
                                name="show_name"
                                value="1"
                                class="sr-only peer"
                                {{ old('show_name', $user->show_name) ? 'checked' : '' }}
                            >
                            <div class="w-9 h-5 bg-white/10 peer-checked:bg-white/40 rounded-full transition-colors"></div>
                            <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white/60 peer-checked:bg-white rounded-full transition-transform peer-checked:translate-x-4"></div>
                        </label>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label for="email" class="text-sm text-white/50">{{ __('messages.email_private') }}</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            required
                            autocomplete="email"
                            class="bg-white/5 border border-white/10 rounded-lg px-3.5 py-2.5 text-sm text-white/85 placeholder-white/20 focus:outline-none focus:border-white/30 transition-colors @error('email') border-red-500/50 @enderror"
                        />
                        @error('email')
                            <p class="text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="border-t border-white/8"></div>

                {{-- Cambio password --}}
                <div class="flex flex-col gap-4">
                    <p class="text-xs font-medium text-white/30 uppercase tracking-widest">{{ __('messages.change_password') }} <span class="normal-case text-white/20">({{ __('messages.optional') }})</span></p>

                    <div class="flex flex-col gap-1.5">
                        <label for="current_password" class="text-sm text-white/50">{{ __('messages.current_password') }}</label>
                        <input
                            id="current_password"
                            type="password"
                            name="current_password"
                            autocomplete="current-password"
                            class="bg-white/5 border border-white/10 rounded-lg px-3.5 py-2.5 text-sm text-white/85 placeholder-white/20 focus:outline-none focus:border-white/30 transition-colors @error('current_password') border-red-500/50 @enderror"
                            placeholder="••••••••"
                        />
                        @error('current_password')
                            <p class="text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label for="password" class="text-sm text-white/50">{{ __('messages.new_password') }}</label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            autocomplete="new-password"
                            class="bg-white/5 border border-white/10 rounded-lg px-3.5 py-2.5 text-sm text-white/85 placeholder-white/20 focus:outline-none focus:border-white/30 transition-colors @error('password') border-red-500/50 @enderror"
                            placeholder="{{ __('messages.password_placeholder') }}"
                        />
                        @error('password')
                            <p class="text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label for="password_confirmation" class="text-sm text-white/50">{{ __('messages.confirm_new_password') }}</label>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            autocomplete="new-password"
                            class="bg-white/5 border border-white/10 rounded-lg px-3.5 py-2.5 text-sm text-white/85 placeholder-white/20 focus:outline-none focus:border-white/30 transition-colors"
                            placeholder="••••••••"
                        />
                    </div>
                </div>

                <div class="border-t border-white/8"></div>

                <button type="submit"
                    class="mt-2 w-full py-2.5 bg-white text-black text-sm font-medium rounded-lg hover:bg-white/90 transition-colors">
                    {{ __('messages.save_changes') }}
                </button>
            </form>

            {{-- Lingua --}}
            <div class="border-t border-white/8 mt-5"></div>
            <div class="flex flex-col gap-4 mt-5">
                <p class="text-xs font-medium text-white/30 uppercase tracking-widest">{{ __('messages.language') }}</p>
                <p class="text-xs text-white/25">{{ __('messages.language_hint') }}</p>
                <div class="flex gap-2">
                    <form method="POST" action="{{ route('locale.switch') }}" class="flex-1">
                        @csrf
                        <input type="hidden" name="locale" value="it">
                        <button type="submit"
                            class="w-full py-2 text-sm rounded-lg border transition-colors {{ app()->getLocale() === 'it' ? 'bg-white/10 text-white/85 border-white/25' : 'text-white/40 border-white/10 hover:text-white/60 hover:border-white/20' }}">
                            🇮🇹 {{ __('messages.lang_it') }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('locale.switch') }}" class="flex-1">
                        @csrf
                        <input type="hidden" name="locale" value="en">
                        <button type="submit"
                            class="w-full py-2 text-sm rounded-lg border transition-colors {{ app()->getLocale() === 'en' ? 'bg-white/10 text-white/85 border-white/25' : 'text-white/40 border-white/10 hover:text-white/60 hover:border-white/20' }}">
                            🇬🇧 {{ __('messages.lang_en') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
