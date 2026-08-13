@forelse($refunds as $refund)
<tr>
    <td data-th="{{ __('Payment') }}" class="text-sm">
        <a href="{{ route('admin.payments.show', $refund->payment_id) }}" class="text-primary hover:underline">
            #{{ Str::limit($refund->payment?->uuid ?? '', 8, '...') }}
        </a>
    </td>
    <td data-th="{{ __('Amount') }}" class="text-sm font-medium text-neutral-900">
        {{ number_format($refund->amount, 2) }} {{ $refund->payment?->currency ?? '' }}
    </td>
    <td data-th="{{ __('Status') }}">
        <div class="flex justify-end lg:justify-start rtl:justify-start">
            @switch($refund->status)
                @case('completed') <x-ui.badge variant="success">{{ __('Completed') }}</x-ui.badge> @break
                @case('pending') <x-ui.badge variant="warning">{{ __('Pending') }}</x-ui.badge> @break
                @case('failed') <x-ui.badge variant="danger">{{ __('Failed') }}</x-ui.badge> @break
                @default <x-ui.badge variant="neutral">{{ ucfirst($refund->status) }}</x-ui.badge>
            @endswitch
        </div>
    </td>
    <td data-th="{{ __('Reason') }}" class="text-sm text-neutral-400">{{ Str::limit($refund->reason, 40) ?: __('—') }}</td>
    <td data-th="{{ __('Date') }}" class="text-sm text-neutral-400">{{ format_date($refund->created_at, true) }}</td>
    <td data-th="{{ __('Actions') }}" class="text-right">
        <x-tables.actions>
            <x-tables.action icon="eye" :href="route('admin.payments.show', $refund->payment_id)" :label="__('View Payment')" />
        </x-tables.actions>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="py-5 text-center text-neutral-400">{{ __('No refunds found.') }}</td>
</tr>
@endforelse
