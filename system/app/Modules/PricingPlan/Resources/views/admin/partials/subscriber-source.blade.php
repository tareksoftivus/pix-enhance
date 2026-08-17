@php
    $payment = $record->reference;
    $isPayment = $payment instanceof \App\Modules\PaymentGateways\Models\Payment;
@endphp

@if($isPayment)
    <div class="flex flex-col items-end gap-1 lg:items-start">
        <x-ui.badge variant="{{ $payment->status === 'completed' ? 'success' : 'warning' }}">
            {{ ucfirst($payment->status) }}
        </x-ui.badge>
        <span class="text-xs text-neutral-500">{{ ucfirst($payment->gateway) }} &middot; {{ number_format($payment->amount, 2) }} {{ $payment->currency }}</span>
    </div>
@else
    <x-ui.badge variant="neutral">{{ __('Free plan') }}</x-ui.badge>
@endif
