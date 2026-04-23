<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Editor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0d0d0d] min-h-screen flex flex-col">
    <script>
        window.__editorContent = @json($content ?: '');
        window.__saveUrl = @json($saveUrl);
    </script>

    {{-- Header --}}
    <header class="flex items-center justify-between px-6 py-3 border-b border-white/10">
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ $backUrl }}" class="text-white/60 text-sm font-medium tracking-wide hover:text-white/80 transition-colors shrink-0">
                ← {{ $project->name }}
            </a>
            <span class="text-white/20 text-sm">/</span>
            <span class="text-white/50 text-sm font-mono truncate">{{ $fileLabel }}</span>
        </div>
        <div class="flex items-center gap-3">
            <span id="save-status" class="text-sm text-white/40"></span>
            <button id="save-btn"
                class="px-4 py-1.5 bg-white text-black text-sm font-medium rounded-lg hover:bg-white/90 disabled:opacity-40 transition-colors">
                {{ __('messages.save') }}
            </button>
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
        </div>
    </header>

    {{-- Main --}}
    <main class="flex flex-col lg:flex-row flex-1 divide-y lg:divide-y-0 lg:divide-x divide-white/10">

        {{-- Editor panel --}}
        <div class="flex flex-col flex-1 min-w-0">
            {{-- Toolbar --}}
            <div class="flex flex-wrap items-center gap-0.5 px-3 py-2 border-b border-white/10">
                <button data-action="bold" class="toolbar-btn font-bold" title="Grassetto">B</button>
                <button data-action="italic" class="toolbar-btn italic" title="Corsivo">I</button>
                <button data-action="strike" class="toolbar-btn line-through" title="Barrato">S</button>
                <button data-action="code" class="toolbar-btn font-mono text-xs" title="Codice inline">&lt;&gt;</button>

                <div class="w-px h-4 bg-white/20 mx-1.5"></div>

                <button data-action="h1" class="toolbar-btn text-xs font-bold" title="Titolo 1">H1</button>
                <button data-action="h2" class="toolbar-btn text-xs font-bold" title="Titolo 2">H2</button>
                <button data-action="h3" class="toolbar-btn text-xs font-bold" title="Titolo 3">H3</button>

                <div class="w-px h-4 bg-white/20 mx-1.5"></div>

                <button data-action="bulletList" class="toolbar-btn" title="Lista puntata">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="9" y1="6" x2="20" y2="6"/><line x1="9" y1="12" x2="20" y2="12"/><line x1="9" y1="18" x2="20" y2="18"/><circle cx="4" cy="6" r="1.5" fill="currentColor" stroke="none"/><circle cx="4" cy="12" r="1.5" fill="currentColor" stroke="none"/><circle cx="4" cy="18" r="1.5" fill="currentColor" stroke="none"/></svg>
                </button>
                <button data-action="orderedList" class="toolbar-btn" title="Lista numerata">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="10" y1="6" x2="21" y2="6"/><line x1="10" y1="12" x2="21" y2="12"/><line x1="10" y1="18" x2="21" y2="18"/><path d="M4 6h1v4" stroke-linecap="round"/><path d="M4 10H5" stroke-linecap="round"/><path d="M4 14h1.5a.5.5 0 0 1 0 1H4a.5.5 0 0 0 0 1h1.5" stroke-linecap="round"/></svg>
                </button>
                <button data-action="blockquote" class="toolbar-btn" title="Citazione">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"/><path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"/></svg>
                </button>
                <button data-action="codeBlock" class="toolbar-btn font-mono text-xs" title="Blocco codice">{ }</button>
                <button data-action="horizontalRule" class="toolbar-btn" title="Separatore">—</button>

                <div class="w-px h-4 bg-white/20 mx-1.5"></div>

                <button data-action="undo" class="toolbar-btn" title="Annulla">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7v6h6"/><path d="M21 17a9 9 0 0 0-9-9 9 9 0 0 0-6 2.3L3 13"/></svg>
                </button>
                <button data-action="redo" class="toolbar-btn" title="Ripeti">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 7v6h-6"/><path d="M3 17a9 9 0 0 1 9-9 9 9 0 0 1 6 2.3l3 2.7"/></svg>
                </button>
            </div>

            {{-- Editor area --}}
            <div id="editor" class="flex-1 overflow-y-auto"></div>

            {{-- Footer --}}
            <div class="flex items-center justify-between px-4 py-2 border-t border-white/10 text-xs text-white/30">
                <span id="word-count">{{ __('messages.words_count', ['n' => 0]) }}</span>
                <span>{{ __('messages.save_shortcut') }}</span>
            </div>
        </div>

        {{-- Markdown source panel --}}
        <div class="flex flex-col lg:w-[42%] min-w-0">
            <div class="flex items-center px-4 py-2.5 border-b border-white/10">
                <span class="text-xs font-medium text-white/40 uppercase tracking-widest">{{ __('messages.markdown_output') }}</span>
            </div>
            <textarea
                id="markdown-output"
                class="flex-1 bg-transparent font-mono text-sm text-white/50 p-4 resize-none focus:outline-none min-h-64 lg:min-h-0"
                readonly
                placeholder="{{ __('messages.markdown_appears_ph') }}"></textarea>
        </div>
    </main>


</body>
</html>
