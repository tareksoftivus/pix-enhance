@php
    $meta = $typeMeta[$event['type']];
@endphp

<div class="timeline__item">
    <span class="timeline__dot" aria-hidden="true"><i data-lucide="{{ $event['icon'] }}"></i></span>

    <div class="setting-row">
        <span class="setting-row__text">
            <span class="setting-row__label">
                {{ $event['title'] }}
                @if ($meta['badge'])
                    <span class="badge badge-sm {{ $meta['badge'] }}">{{ $meta['label'] }}</span>
                @endif
            </span>
            <span class="setting-row__hint">{{ $event['detail'] }} &middot; {{ $event['when'] }}</span>
        </span>

        @if ($event['meta'])
            <span class="setting-row__control">
                <span class="data-table__num data-table__strong">{{ $event['meta'] }}</span>
            </span>
        @endif
    </div>
</div>
