<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.laws_title') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0d0d0d] min-h-screen flex flex-col">

    <header class="flex items-center justify-between px-6 py-3 border-b border-white/10">
        <a href="{{ route('home') }}" class="text-white/60 text-sm font-medium tracking-wide hover:text-white/80 transition-colors">
            {{ __('messages.nav_home') }}
        </a>
        <div class="flex items-center gap-3">
            <a href="{{ route('users.show', Auth::user()) }}" class="text-white/40 text-sm hover:text-white/60 transition-colors">{{ Auth::user()->displayName() }}</a>
            <a href="{{ route('users.index') }}" class="text-white/40 text-sm hover:text-white/60 transition-colors">{{ __('messages.nav_users') }}</a>
            <a href="{{ route('stars.index') }}" class="text-white/40 text-sm hover:text-white/60 transition-colors">{{ __('messages.nav_stars') }}</a>
            <a href="{{ route('my_projects.index') }}" class="text-white/40 text-sm hover:text-white/60 transition-colors">{{ __('messages.nav_mine') }}</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="px-4 py-1.5 text-sm font-medium text-white/60 border border-white/15 rounded-lg hover:border-white/30 hover:text-white/80 transition-colors">
                    {{ __('messages.logout') }}
                </button>
            </form>
        </div>
    </header>

    <main class="flex-1 px-6 py-10 max-w-4xl mx-auto w-full">

        @if (session('error'))
            <div class="mb-6 px-4 py-3 rounded-lg bg-red-500/8 border border-red-500/15 text-red-400/80 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-white/85">{{ __('messages.laws_title') }}</h1>
            <button
                onclick="document.getElementById('modal-create').classList.remove('hidden')"
                class="px-4 py-2 bg-white text-black text-sm font-medium rounded-lg hover:bg-white/90 transition-colors"
            >
                {{ __('messages.new_law_btn') }}
            </button>
        </div>

        {{-- Ricerca --}}
        <form method="GET" action="{{ route('projects.index') }}" class="mb-4">
            <input type="hidden" name="filter" value="{{ $filter }}">
            <input type="hidden" name="sort" value="{{ $sort }}">
            <div class="flex gap-2">
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="{{ __('messages.search_laws') }}"
                    class="flex-1 bg-white/5 border border-white/10 rounded-lg px-3.5 py-2 text-sm text-white/85 placeholder-white/20 focus:outline-none focus:border-white/30 transition-colors"
                />
                <button type="submit" class="px-4 py-2 bg-white/8 border border-white/10 rounded-lg text-sm text-white/60 hover:bg-white/12 hover:text-white/80 transition-colors">
                    {{ __('messages.search') }}
                </button>
            </div>
        </form>

        {{-- Filtri e ordinamento --}}
        <div class="flex items-center justify-between mb-6 gap-4 flex-wrap">

            {{-- Filter tabs --}}
            <div class="flex items-center gap-1">
                @foreach ([
                    'all'     => __('messages.filter_all'),
                    'mine'    => __('messages.filter_mine'),
                    'invited' => __('messages.filter_invited'),
                    'other'   => __('messages.filter_other'),
                ] as $key => $label)
                    <a href="{{ route('projects.index', ['filter' => $key, 'sort' => $sort, 'search' => $search]) }}"
                       class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors {{ $filter === $key ? 'bg-white/10 text-white/85' : 'text-white/35 hover:text-white/60 hover:bg-white/5' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- Sort --}}
            <div class="flex items-center gap-1">
                @foreach ([
                    'created' => __('messages.sort_created'),
                    'updated' => __('messages.sort_updated'),
                    'stars'   => __('messages.sort_stars'),
                ] as $key => $label)
                    <a href="{{ route('projects.index', ['sort' => $key, 'filter' => $filter, 'search' => $search]) }}"
                       class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors {{ $sort === $key ? 'bg-white/10 text-white/85' : 'text-white/35 hover:text-white/60 hover:bg-white/5' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        @if ($projects->isEmpty())
            <div class="text-center py-20 text-white/30 text-sm">
                {{ __('messages.no_laws_found') }}
            </div>
        @else
            <div class="flex flex-col gap-3">
                @foreach ($projects as $project)
                    <div class="flex items-start gap-3 px-5 py-4 border border-white/10 rounded-xl hover:border-white/20 hover:bg-white/3 transition-all group">
                        <a href="{{ route('projects.show', $project) }}" class="flex-1 min-w-0">
                            <div class="flex items-start gap-2">
                                <div class="flex-1 min-w-0">
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
                                        <span>{{ $project->created_at->diffForHumans() }}</span>
                                        @if ($project->stars_count > 0)
                                            <span>·</span>
                                            <span>★ {{ $project->stars_count }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>

                        {{-- Star toggle --}}
                        <form method="POST" action="{{ route('projects.star', $project) }}" class="shrink-0 self-center">
                            @csrf
                            <button type="submit"
                                class="px-2.5 py-1 text-sm rounded-md border transition-colors {{ isset($starredIds[$project->id]) ? 'text-yellow-400 border-yellow-400/30 hover:border-yellow-400/50' : 'text-white/25 border-white/10 hover:text-white/60 hover:border-white/25' }}">
                                ★
                            </button>
                        </form>
                    </div>
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
