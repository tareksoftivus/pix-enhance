@props([
    'title' => 'Main Menu',
])

<div class="space-y-2">
    {{-- 'Main Menu' is the implicit default group — top-level items need no heading. --}}
    @if ($title !== '' && $title !== 'Main Menu')
        <h3 class="nav-group-title">{{ __($title) }}</h3>
    @endif
    <nav class="space-y-2">
        {{ $slot }}
    </nav>
</div>
