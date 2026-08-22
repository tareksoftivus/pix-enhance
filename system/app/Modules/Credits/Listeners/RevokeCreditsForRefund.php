<?php

namespace App\Modules\Credits\Listeners;

use App\Models\User;
use App\Modules\Credits\Services\CreditService;
use App\Modules\PaymentGateways\Events\RefundProcessed;
use App\Modules\SystemNotifications\Services\UserSystemNotificationService;

class RevokeCreditsForRefund
{
    public function __construct(
        protected CreditService $credits,
        protected UserSystemNotificationService $systemNotifications
    ) {}

    public function handle(RefundProcessed $event): void
    {
        $refund = $event->refund->loadMissing('payment');
        $payment = $refund->payment;
        $metadata = $payment?->metadata ?? [];
        $purchasedCredits = (int) ($metadata['credits'] ?? 0);

        if (! $payment || ! filter_var($metadata['credits_module'] ?? false, FILTER_VALIDATE_BOOL) || $purchasedCredits <= 0 || $payment->user_type !== User::class || ! $payment->user_id) {
            return;
        }

        $user = User::query()->find($payment->user_id);

        if (! $user) {
            return;
        }

        $ratio = (float) $refund->amount / max((float) $payment->amount, 0.01);
        $creditsToRevoke = (int) ceil($purchasedCredits * min(1, $ratio));

        $transaction = $this->credits->revoke(
            $user,
            $creditsToRevoke,
            'payment_refund',
            $refund,
            array_merge($metadata, [
                'refund_id' => $refund->id,
                'payment_id' => $payment->id,
            ]),
            'refund:'.$refund->id.':credits'
        );

        if ($transaction) {
            $this->systemNotifications->creditsRevoked($user, $transaction);
            $this->systemNotifications->creditsLow($user, $this->credits->summaryFor($user)['available']);
        }
    }
}
