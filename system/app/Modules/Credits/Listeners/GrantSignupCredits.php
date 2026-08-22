<?php

namespace App\Modules\Credits\Listeners;

use App\Models\User;
use App\Modules\Credits\Services\CreditService;
use App\Modules\SystemNotifications\Services\UserSystemNotificationService;
use Illuminate\Auth\Events\Registered;

class GrantSignupCredits
{
    public function __construct(
        protected CreditService $credits,
        protected UserSystemNotificationService $systemNotifications
    ) {}

    public function handle(Registered $event): void
    {
        if (! $event->user instanceof User) {
            return;
        }

        $amount = (int) config('credits.signup_credits', 0);

        if ($amount <= 0) {
            return;
        }

        $transaction = $this->credits->grant(
            $event->user,
            $amount,
            'signup_bonus',
            null,
            ['source' => 'registration'],
            'signup:user:'.$event->user->id
        );

        $this->systemNotifications->creditsGranted($event->user, $transaction);
    }
}
