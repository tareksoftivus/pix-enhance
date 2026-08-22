<?php

namespace App\Modules\Credits\Listeners;

use App\Modules\Credits\Models\CreditOrder;
use App\Modules\PaymentGateways\Events\PaymentCreated;
use App\Modules\PaymentGateways\Events\PaymentFailed;
use App\Modules\PaymentGateways\Events\PaymentSucceeded;
use Illuminate\Support\Facades\Schema;

class SyncCreditOrderForPaymentStatus
{
    public function handle(PaymentCreated|PaymentSucceeded|PaymentFailed $event): void
    {
        if (! Schema::hasTable('credit_orders')) {
            return;
        }

        $payment = $event->payment->fresh();

        if (! $payment) {
            return;
        }

        $metadata = $payment->metadata ?? [];
        $order = null;

        if (! empty($metadata['credit_order_id'])) {
            $order = CreditOrder::query()->find($metadata['credit_order_id']);
        }

        $order ??= CreditOrder::query()->where('payment_id', $payment->id)->first();

        if (! $order) {
            return;
        }

        $order->update([
            'status' => match ($payment->status) {
                'completed' => 'completed',
                'failed' => 'failed',
                'canceled', 'cancelled' => 'cancelled',
                default => 'pending',
            },
        ]);
    }
}
