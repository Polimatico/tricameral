<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.my_laws_title') }}</title>
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

        <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl font-bold text-white/85">{{ __('messages.my_laws_title') }}</h1>
            <button
                onclick="document.getElementById('modal-create').classList.remove('hidden')"
                class="px-4 py-2 bg-white text-black text-sm font-medium rounded-lg hover:bg-white/90 transition-colors"
            >
                {{ __('messages.new_law_modal_btn') }}
            </button>
        </div>

        @if ($projects->isEmpty())
            <div class="text-center py-20 text-white/30 text-sm">
                {{ __('messages.no_laws_yet') }}
            </div>
        @else
            <div class="flex flex-col gap-3">
                @foreach ($projects as $project)
                    <a href="{{ route('projects.show', $project) }}"
                        class="flex items-start justify-between px-5 py-4 border border-white/10 rounded-xl hover:border-white/20 hover:bg-white/3 transition-all group">
                        <div class="flex flex-col gap-1">
                            <span class="text-white/85 font-medium group-hover:text-white transition-colors">{{ $project->name }}</span>
                            @if ($project->description)
                                <span class="text-white/35 text-sm">{{ $project->description }}</span>
                            @endif
                        </div>
                        <span class="text-white/25 text-xs mt-1 shrink-0 ml-4">{{ $project->created_at->diffForHumans() }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </main>

    {{-- Modal crea legge --}}
    <div id="modal-create" class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50 px-4">
        <div class="bg-[#111] border border-white/10 rounded-2xl w-full max-w-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-white/85 font-semibold">{{ __('messages.new_law_modal_title') }}</h2>
                <button
                    onclick="document.getElementById('modal-create').classList.add('hidden')"
                    class="text-white/30 hover:text-white/60 transition-colors text-lg leading-none"
                >✕</button>
            </div>

            <form method="POST" action="{{ route('projects.store') }}" class="flex flex-col gap-4">
                @csrf

                <div class="flex flex-col gap-1.5">
                    <label for="name" class="text-sm text-white/50">{{ __('messages.law_name_label') }}</label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        placeholder="{{ __('messages.law_name_example') }}"
                        class="bg-white/5 border border-white/10 rounded-lg px-3.5 py-2.5 text-sm text-white/85 placeholder-white/20 focus:outline-none focus:border-white/30 transition-colors @error('name') border-red-500/50 @enderror"
                    />
                    @error('name')
                        <p class="text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="description" class="text-sm text-white/50">{{ __('messages.law_desc_label') }} <span class="text-white/20">({{ __('messages.optional') }})</span></label>
                    <textarea
                        id="description"
                        name="description"
                        rows="3"
                        placeholder="{{ __('messages.law_desc_placeholder') }}"
                        class="bg-white/5 border border-white/10 rounded-lg px-3.5 py-2.5 text-sm text-white/85 placeholder-white/20 focus:outline-none focus:border-white/30 transition-colors resize-none @error('description') border-red-500/50 @enderror"
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="mt-1 w-full py-2.5 bg-white text-black text-sm font-medium rounded-lg hover:bg-white/90 transition-colors">
                    {{ __('messages.create_law_btn') }}
                </button>
            </form>
        </div>
    </div>

    @if ($errors->any())
        <script>document.getElementById('modal-create').classList.remove('hidden');</script>
    @endif

</body>
</html>
