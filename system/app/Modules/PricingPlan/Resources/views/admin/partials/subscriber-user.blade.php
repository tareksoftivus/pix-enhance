@php
    $transaction = $record;
@endphp

<div class="flex items-center justify-end gap-3 lg:justify-start rtl:justify-start">
    <div class="bg-primary/10 text-primary flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-sm font-bold">
        {{ strtoupper(substr($transaction->user?->name ?? $transaction->user?->email ?? '?', 0, 1)) }}
    </div>
    <div class="min-w-0 text-right lg:text-left">
        <p class="truncate text-sm font-bold text-neutral-950">{{ $transaction->user?->name ?? __('Deleted user') }}</p>
        <p class="truncate text-xs text-neutral-500">{{ $transaction->user?->email ?? __('No email') }}</p>
    </div>
</div>
