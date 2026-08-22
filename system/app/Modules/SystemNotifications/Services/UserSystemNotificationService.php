<?php

namespace App\Modules\SystemNotifications\Services;

use App\Models\User;
use App\Modules\Credits\Models\CreditTransaction;
use App\Modules\PaymentGateways\Models\Payment;
use App\Modules\PaymentGateways\Models\Refund;
use App\Modules\RenderJobs\Models\RenderJob;
use App\Modules\Support\Models\SupportTicket;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class UserSystemNotificationService
{
    public function __construct(
        protected SystemNotificationService $notifications
    ) {}

    public function welcome(User $user): void
    {
        $this->notifications->sendOnce($user, [
            'title' => __('Welcome to :app', ['app' => config('app.name')]),
            'body' => __('Your workspace is ready. You can upload an image and start enhancing.'),
            'icon' => 'sparkles',
            'type' => 'success',
            'url' => $this->routeOrNull('user.dashboard'),
        ], 'account.welcome', 'user:'.$user->id.':welcome');
    }

    public function profileUpdated(User $user): void
    {
        $this->notifications->sendOnce($user, [
            'title' => __('Profile updated'),
            'body' => __('Your account details were saved successfully.'),
            'icon' => 'user-check',
            'type' => 'success',
            'url' => $this->routeOrNull('user.settings'),
        ], 'account.profile_updated', 'user:'.$user->id.':profile:'.$user->updated_at?->timestamp);
    }

    public function emailVerificationRequired(User $user): void
    {
        $this->notifications->sendOnce($user, [
            'title' => __('Verify your new email'),
            'body' => __('We sent a verification link to your new email address.'),
            'icon' => 'mail-check',
            'type' => 'warning',
            'url' => $this->routeOrNull('verification.notice'),
        ], 'account.email_verification_required', 'user:'.$user->id.':email:'.$user->email);
    }

    public function phoneVerificationRequired(User $user): void
    {
        $this->notifications->sendOnce($user, [
            'title' => __('Verify your phone number'),
            'body' => __('Your phone number changed. Complete verification to keep SMS features available.'),
            'icon' => 'smartphone',
            'type' => 'warning',
            'url' => $this->routeOrNull('user.phone.verification.notice'),
        ], 'account.phone_verification_required', 'user:'.$user->id.':phone:'.$user->phone);
    }

    public function passwordChanged(User $user, ?string $ip = null): void
    {
        $body = $ip
            ? __('Your password was changed from :ip. If this was not you, contact support immediately.', ['ip' => $ip])
            : __('Your password was changed. If this was not you, contact support immediately.');

        $this->notifications->sendOnce($user, [
            'title' => __('Password changed'),
            'body' => $body,
            'icon' => 'lock-keyhole',
            'type' => 'warning',
            'url' => $this->routeOrNull('user.settings'),
        ], 'security.password_changed', 'user:'.$user->id.':password:'.$user->updated_at?->timestamp);
    }

    public function sessionsRevoked(User $user): void
    {
        $this->notifications->send($user, [
            'title' => __('Other sessions signed out'),
            'body' => __('All other active sessions for your account were revoked.'),
            'icon' => 'shield-check',
            'type' => 'success',
            'url' => $this->routeOrNull('user.settings'),
        ], 'security.sessions_revoked');
    }

    public function sessionRevoked(User $user): void
    {
        $this->notifications->send($user, [
            'title' => __('Session signed out'),
            'body' => __('One active session was revoked from your account.'),
            'icon' => 'shield-check',
            'type' => 'success',
            'url' => $this->routeOrNull('user.settings'),
        ], 'security.session_revoked');
    }

    public function workspacePreferencesUpdated(User $user, string $section): void
    {
        $this->notifications->send($user, [
            'title' => __('Workspace preferences updated'),
            'body' => __('Your :section settings were saved.', ['section' => $section]),
            'icon' => 'settings',
            'type' => 'success',
            'url' => $this->routeOrNull('user.settings'),
        ], 'account.preferences_updated');
    }

    public function renderCompleted(RenderJob $job): void
    {
        $user = $job->user;

        if (! $user) {
            return;
        }

        $this->notifications->sendOnce($user, [
            'title' => __('Enhancement complete'),
            'body' => __(':file is ready to view and download.', ['file' => $job->displayName()]),
            'icon' => 'circle-check',
            'type' => 'success',
            'url' => $this->routeOrNull('user.render-jobs.show', $job),
        ], 'render.completed', 'render:'.$job->id.':completed');
    }

    public function renderFailed(RenderJob $job): void
    {
        $user = $job->user;

        if (! $user) {
            return;
        }

        $this->notifications->sendOnce($user, [
            'title' => __('Enhancement failed'),
            'body' => __(':file could not be processed. Credits reserved for this job were released.', ['file' => $job->displayName()]),
            'icon' => 'circle-alert',
            'type' => 'danger',
            'url' => $this->routeOrNull('user.render-jobs.show', $job),
        ], 'render.failed', 'render:'.$job->id.':failed');
    }

    public function renderCancelled(RenderJob $job): void
    {
        $user = $job->user;

        if (! $user) {
            return;
        }

        $this->notifications->sendOnce($user, [
            'title' => __('Enhancement cancelled'),
            'body' => __(':file was cancelled and any reserved credits were released.', ['file' => $job->displayName()]),
            'icon' => 'circle-slash',
            'type' => 'info',
            'url' => $this->routeOrNull('user.projects'),
        ], 'render.cancelled', 'render:'.$job->id.':cancelled');
    }

    public function paymentCreated(Payment $payment): void
    {
        $user = $this->paymentUser($payment);

        if (! $user) {
            return;
        }

        $this->notifications->sendOnce($user, [
            'title' => __('Payment started'),
            'body' => __('Your :amount payment is pending with :gateway.', [
                'amount' => $this->formatMoney($payment),
                'gateway' => Str::headline($payment->gateway),
            ]),
            'icon' => 'credit-card',
            'type' => 'info',
            'url' => $this->routeOrNull('user.billing.payments.show', $payment->uuid),
        ], 'billing.payment_created', 'payment:'.$payment->id.':created');
    }

    public function paymentSucceeded(Payment $payment): void
    {
        $user = $this->paymentUser($payment);

        if (! $user) {
            return;
        }

        $this->notifications->sendOnce($user, [
            'title' => __('Payment completed'),
            'body' => __('Your :amount payment was completed successfully.', ['amount' => $this->formatMoney($payment)]),
            'icon' => 'badge-check',
            'type' => 'success',
            'url' => $this->routeOrNull('user.billing.payments.show', $payment->uuid),
        ], 'billing.payment_succeeded', 'payment:'.$payment->id.':succeeded');
    }

    public function paymentFailed(Payment $payment): void
    {
        $user = $this->paymentUser($payment);

        if (! $user) {
            return;
        }

        $this->notifications->sendOnce($user, [
            'title' => __('Payment failed'),
            'body' => __('Your :amount payment could not be completed. You can try again from Billing.', ['amount' => $this->formatMoney($payment)]),
            'icon' => 'circle-alert',
            'type' => 'danger',
            'url' => $this->routeOrNull('user.billing'),
        ], 'billing.payment_failed', 'payment:'.$payment->id.':failed');
    }

    public function refundProcessed(Refund $refund): void
    {
        $payment = $refund->payment;
        $user = $payment ? $this->paymentUser($payment) : null;

        if (! $user) {
            return;
        }

        $this->notifications->sendOnce($user, [
            'title' => __('Refund processed'),
            'body' => __('A :amount refund was processed for your payment.', [
                'amount' => number_format((float) $refund->amount, 2).' '.strtoupper((string) $payment->currency),
            ]),
            'icon' => 'undo-2',
            'type' => 'info',
            'url' => $this->routeOrNull('user.billing.payments.show', $payment->uuid),
        ], 'billing.refund_processed', 'refund:'.$refund->id.':processed');
    }

    public function creditsGranted(User $user, CreditTransaction $transaction): void
    {
        $amount = abs((int) $transaction->amount);

        $this->notifications->sendOnce($user, [
            'title' => __('Credits added'),
            'body' => trans_choice(':count credit was added to your wallet.|:count credits were added to your wallet.', $amount, ['count' => number_format($amount)]),
            'icon' => 'coins',
            'type' => 'success',
            'url' => $this->routeOrNull('user.billing'),
        ], 'credits.granted', 'credit-transaction:'.$transaction->id.':granted');
    }

    public function creditsRevoked(User $user, CreditTransaction $transaction): void
    {
        $amount = abs((int) $transaction->amount);

        $this->notifications->sendOnce($user, [
            'title' => __('Credits adjusted'),
            'body' => trans_choice(':count credit was removed from your wallet.|:count credits were removed from your wallet.', $amount, ['count' => number_format($amount)]),
            'icon' => 'coins',
            'type' => 'warning',
            'url' => $this->routeOrNull('user.billing'),
        ], 'credits.revoked', 'credit-transaction:'.$transaction->id.':revoked');
    }

    public function creditsLow(User $user, int $availableCredits): void
    {
        $threshold = (int) config('credits.low_balance_threshold', 10);

        if ($threshold < 1 || $availableCredits > $threshold || ! $this->userWantsCreditAlerts($user)) {
            return;
        }

        $this->notifications->sendOnce($user, [
            'title' => __('Credits running low'),
            'body' => trans_choice('You have :count credit available.|You have :count credits available.', max(0, $availableCredits), ['count' => number_format(max(0, $availableCredits))]),
            'icon' => 'coins',
            'type' => 'warning',
            'url' => $this->routeOrNull('user.billing'),
        ], 'credits.low', 'user:'.$user->id.':credits-low:'.now()->toDateString());
    }

    public function supportTicketOpened(SupportTicket $ticket): void
    {
        $user = $ticket->user;

        if (! $user) {
            return;
        }

        $this->notifications->sendOnce($user, [
            'title' => __('Support ticket opened'),
            'body' => __('Ticket :reference was submitted. Support will reply in the thread.', ['reference' => $ticket->reference]),
            'icon' => 'life-buoy',
            'type' => 'success',
            'url' => $this->routeOrNull('user.support-tickets.show', $ticket),
        ], 'support.ticket_opened', 'support-ticket:'.$ticket->id.':opened');
    }

    public function supportStaffReplied(SupportTicket $ticket): void
    {
        $user = $ticket->user;

        if (! $user) {
            return;
        }

        $this->notifications->send($user, [
            'title' => __('Support replied'),
            'body' => __('There is a new staff reply on ticket :reference.', ['reference' => $ticket->reference]),
            'icon' => 'message-circle',
            'type' => 'info',
            'url' => $this->routeOrNull('user.support-tickets.show', $ticket),
        ], 'support.staff_replied');
    }

    public function supportStatusChanged(SupportTicket $ticket): void
    {
        $user = $ticket->user;

        if (! $user) {
            return;
        }

        $label = SupportTicket::statuses()[$ticket->status]['label'] ?? Str::headline($ticket->status);
        $type = in_array($ticket->status, ['resolved', 'closed'], true) ? 'success' : 'info';

        $this->notifications->send($user, [
            'title' => __('Support ticket updated'),
            'body' => __('Ticket :reference is now :status.', ['reference' => $ticket->reference, 'status' => $label]),
            'icon' => 'life-buoy',
            'type' => $type,
            'url' => $this->routeOrNull('user.support-tickets.show', $ticket),
        ], 'support.status_changed');
    }

    protected function paymentUser(Payment $payment): ?User
    {
        return $payment->user instanceof User ? $payment->user : null;
    }

    protected function formatMoney(Payment $payment): string
    {
        return number_format((float) $payment->amount, 2).' '.strtoupper((string) $payment->currency);
    }

    protected function userWantsCreditAlerts(User $user): bool
    {
        $preference = $user->workspacePreference()->first();

        return (bool) data_get($preference?->notification_preferences ?? [], 'credits_low', true);
    }

    protected function routeOrNull(string $name, mixed ...$parameters): ?string
    {
        return Route::has($name) ? route($name, $parameters) : null;
    }
}
