<?php

namespace App\Modules\Credits\Listeners;

use App\Models\User;
use App\Modules\Credits\Services\CreditService;
use App\Modules\PaymentGateways\Events\PaymentSucceeded;
use App\Modules\SystemNotifications\Services\UserSystemNotificationService;

class GrantCreditsForSuccessfulPayment
{
    public function __construct(
        protected CreditService $credits,
        protected UserSystemNotificationService $systemNotifications
    ) {}

    public function handle(PaymentSucceeded $event): void
    {
        $payment = $event->payment->fresh();

        if (! $payment) {
            return;
        }

        $metadata = $payment->metadata ?? [];
        $amount = (int) ($metadata['credits'] ?? 0);

        if (! filter_var($metadata['credits_module'] ?? false, FILTER_VALIDATE_BOOL) || $amount <= 0 || $payment->user_type !== User::class || ! $payment->user_id) {
            return;
        }

        $user = User::query()->find($payment->user_id);

        if (! $user) {
            return;
        }

        $transaction = $this->credits->grant(
            $user,
            $amount,
            (string) ($metadata['credits_reason'] ?? 'payment_credit'),
            $payment,
            $metadata,
            'payment:'.$payment->id.':credits'
        );

        $this->systemNotifications->creditsGranted($user, $transaction);
    }
}
