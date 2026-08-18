@php
    $meta = $typeMeta[$event['type']];
    $url = $event['url'] ?? null;
    $occurredAt = $event['occurred_at'] ?? null;
@endphp

<div class="timeline__item">
    <span class="timeline__dot" aria-hidden="true"><i data-lucide="{{ $event['icon'] }}"></i></span>

    <div class="setting-row">
        <span class="setting-row__text">
            <span class="setting-row__label">
                @if ($url)
                    <a class="btn-link btn-link-sm" href="{{ $url }}">
                        {{ $event['title'] }}
                        <i data-lucide="arrow-right"></i>
                    </a>
                @else
                    {{ $event['title'] }}
                @endif

                @if ($meta['badge'])
                    <span class="badge badge-sm {{ $meta['badge'] }}">{{ $meta['label'] }}</span>
                @endif
            </span>
            <span class="setting-row__hint">
                {{ $event['detail'] }}
                &middot;
                @if ($occurredAt)
                    <time datetime="{{ $occurredAt->toIso8601String() }}" title="{{ $event['date_title'] ?? null }}">
                        {{ $event['when'] }}
                    </time>
                @else
                    {{ $event['when'] }}
                @endif
            </span>
        </span>

        @if (! empty($event['meta']))
            <span class="setting-row__control">
                <span class="data-table__num data-table__strong">{{ $event['meta'] }}</span>
            </span>
        @endif
    </div>
</div>
