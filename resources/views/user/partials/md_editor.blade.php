@php
    $editorId = 'md-editor-' . uniqid();
    $textareaClass = $size === 'xs'
        ? 'text-xs text-white/80'
        : 'text-sm text-white/80';
@endphp

<div class="md-editor" id="{{ $editorId }}">
    {{-- Tabs --}}
    <div class="flex items-center gap-0 mb-1.5 border-b border-white/8">
        <button type="button"
            class="md-tab md-tab-write px-3 py-1.5 text-xs font-medium text-white/60 border-b-2 border-white/40 -mb-px transition-colors"
            onclick="mdTabSwitch('{{ $editorId }}', 'write')">
            Scrivi
        </button>
        <button type="button"
            class="md-tab md-tab-preview px-3 py-1.5 text-xs font-medium text-white/25 border-b-2 border-transparent -mb-px hover:text-white/50 transition-colors"
            onclick="mdTabSwitch('{{ $editorId }}', 'preview')">
            Anteprima
        </button>
    </div>

    {{-- Write panel --}}
    <div class="md-panel-write">
        <textarea
            name="{{ $name }}"
            rows="{{ $rows }}"
            placeholder="{{ $placeholder }}"
            class="w-full bg-white/4 border border-white/10 rounded-lg px-3 py-2.5 {{ $textareaClass }} placeholder-white/20 focus:outline-none focus:border-white/25 resize-y font-mono"
        >{{ $value }}</textarea>
    </div>

    {{-- Preview panel --}}
    <div class="md-panel-preview hidden">
        <div class="min-h-20 bg-white/2 border border-white/8 rounded-lg px-3 py-2.5 md-rendered {{ $textareaClass }} leading-relaxed text-white/55">
        </div>
    </div>
</div>
