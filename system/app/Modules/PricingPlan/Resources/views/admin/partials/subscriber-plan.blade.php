@php
    $metadata = $record->metadata ?? [];
@endphp

<div>
    <p class="text-sm font-semibold text-neutral-950">{{ $metadata['pricing_plan_name'] ?? __('Unknown plan') }}</p>
    <p class="text-xs text-neutral-500">{{ $metadata['pricing_plan_slug'] ?? __('No slug') }}</p>
</div>
