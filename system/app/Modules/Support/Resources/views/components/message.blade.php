@props([
    'name',
    'body',
    'at',
    'staff' => false,
])

{{--
    A single conversation entry (the ticket's opening message or a reply).
    Kept as its own component so the initial server render and the AJAX
    "load older" responses produce byte-identical markup.
--}}
<div class="support-message flex gap-4 border-b border-neutral-100 py-4 last:border-b-0">
    {{-- Avatar --}}
    <div @class([
        'flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-sm font-semibold',
        'bg-primary text-white' => $staff,
        'bg-neutral-100 text-neutral-600' => ! $staff,
    ])>
        {{ mb_strtoupper(mb_substr($name, 0, 1)) }}
    </div>

    {{-- Body --}}
    <div class="min-w-0 flex-1">
        <div class="mb-1.5 flex flex-wrap items-center gap-2">
            <span class="text-sm font-semibold text-neutral-900">{{ $name }}</span>
            @if ($staff)
                <span class="rounded-full bg-primary/10 px-2 py-0.5 text-xs font-medium text-primary">{{ __('Staff') }}</span>
            @endif
            <time class="text-xs text-neutral-400">{{ format_date($at, true) }}</time>
        </div>
        {{-- Body is flush with the tags: whitespace-pre-line would otherwise
             render the template's own newline/indent as a phantom blank line. --}}
        <div @class([
            'text-sm leading-relaxed whitespace-pre-line',
            'text-neutral-800' => $staff,
            'text-neutral-700' => ! $staff,
        ])>{{ trim($body) }}</div>
    </div>
</div>
