@props([
    'name',
    'body',
    'at',
    'staff' => false,
])

<div @class(['support-message', 'support-message-staff' => $staff])>
    <div class="support-message__avatar" aria-hidden="true">
        {{ mb_strtoupper(mb_substr($name, 0, 1)) }}
    </div>

    <div class="support-message__body">
        <div class="support-message__head">
            <span class="support-message__name">{{ $name }}</span>
            @if ($staff)
                <span class="badge badge-sm badge-primary">{{ __('Staff') }}</span>
            @endif
            <time class="support-message__time">{{ format_date($at, true) }}</time>
        </div>
        <div class="support-message__text">{{ trim($body) }}</div>
    </div>
</div>
