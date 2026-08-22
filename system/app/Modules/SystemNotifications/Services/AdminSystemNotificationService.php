<?php

namespace App\Modules\SystemNotifications\Services;

use App\Models\Admin;
use App\Models\User;
use App\Modules\PaymentGateways\Models\Payment;
use App\Modules\PaymentGateways\Models\Refund;
use App\Modules\RenderJobs\Models\RenderJob;
use App\Modules\Support\Models\SupportTicket;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class AdminSystemNotificationService
{
    public function __construct(
        protected SystemNotificationService $notifications
    ) {}

    public function userRegistered(User $user): void
    {
        $this->notifyAdmins([
            'title' => __('New user registered'),
            'body' => __(':name joined with :email.', [
                'name' => $user->name,
                'email' => $user->email,
            ]),
            'icon' => 'ph-user-plus',
            'type' => 'success',
            'url' => $this->routeOrNull('admin.users.show', $user),
        ], 'admin.user_registered', 'user:'.$user->id.':registered');
    }

    public function userCreatedByAdmin(User $user): void
    {
        $this->notifyAdmins([
            'title' => __('User account created'),
            'body' => __(':name was added by an admin.', ['name' => $user->name]),
            'icon' => 'ph-user-plus',
            'type' => 'success',
            'url' => $this->routeOrNull('admin.users.show', $user),
        ], 'admin.user_created', 'user:'.$user->id.':created');
    }

    public function profileUpdated(Admin $admin): void
    {
        $this->notifications->sendOnce($admin, [
            'title' => __('Admin profile updated'),
            'body' => __('Your admin profile details were saved successfully.'),
            'icon' => 'ph-user-check',
            'type' => 'success',
            'url' => $this->routeOrNull('admin.profile.edit'),
        ], 'admin.profile_updated', 'admin:'.$admin->id.':profile:'.$admin->updated_at?->timestamp);
    }

    public function passwordChanged(Admin $admin, ?string $ip = null): void
    {
        $body = $ip
            ? __('Your admin password was changed from :ip. If this was not you, review your account immediately.', ['ip' => $ip])
            : __('Your admin password was changed. If this was not you, review your account immediately.');

        $this->notifications->sendOnce($admin, [
            'title' => __('Admin password changed'),
            'body' => $body,
            'icon' => 'ph-lock-key',
            'type' => 'warning',
            'url' => $this->routeOrNull('admin.profile.edit'),
        ], 'admin.password_changed', 'admin:'.$admin->id.':password:'.$admin->updated_at?->timestamp);
    }

    public function sessionRevoked(Admin $admin): void
    {
        $this->notifications->send($admin, [
            'title' => __('Session signed out'),
            'body' => __('One active admin session was revoked from your account.'),
            'icon' => 'ph-shield-check',
            'type' => 'success',
            'url' => $this->routeOrNull('admin.profile.edit'),
        ], 'admin.session_revoked');
    }

    public function sessionsRevoked(Admin $admin): void
    {
        $this->notifications->send($admin, [
            'title' => __('Other sessions signed out'),
            'body' => __('All other active admin sessions were revoked.'),
            'icon' => 'ph-shield-check',
            'type' => 'success',
            'url' => $this->routeOrNull('admin.profile.edit'),
        ], 'admin.sessions_revoked');
    }

    public function paymentCreated(Payment $payment): void
    {
        $payment->loadMissing('user');

        $this->notifyAdmins([
            'title' => __('Payment pending'),
            'body' => __(':amount payment from :customer is pending with :gateway.', [
                'amount' => $this->formatMoney($payment),
                'customer' => $this->customerName($payment),
                'gateway' => Str::headline($payment->gateway),
            ]),
            'icon' => 'ph-credit-card',
            'type' => 'info',
            'url' => $this->routeOrNull('admin.payments.show', $payment),
        ], 'admin.payment_created', 'payment:'.$payment->id.':created');
    }

    public function paymentSucceeded(Payment $payment): void
    {
        $payment->loadMissing('user');

        $this->notifyAdmins([
            'title' => __('Payment completed'),
            'body' => __(':customer completed a :amount payment.', [
                'customer' => $this->customerName($payment),
                'amount' => $this->formatMoney($payment),
            ]),
            'icon' => 'ph-seal-check',
            'type' => 'success',
            'url' => $this->routeOrNull('admin.payments.show', $payment),
        ], 'admin.payment_succeeded', 'payment:'.$payment->id.':succeeded');
    }

    public function paymentFailed(Payment $payment): void
    {
        $payment->loadMissing('user');

        $this->notifyAdmins([
            'title' => __('Payment failed'),
            'body' => __(':customer had a failed :amount payment.', [
                'customer' => $this->customerName($payment),
                'amount' => $this->formatMoney($payment),
            ]),
            'icon' => 'ph-warning-circle',
            'type' => 'danger',
            'url' => $this->routeOrNull('admin.payments.show', $payment),
        ], 'admin.payment_failed', 'payment:'.$payment->id.':failed');
    }

    public function refundProcessed(Refund $refund): void
    {
        $refund->loadMissing('payment.user');
        $payment = $refund->payment;

        if (! $payment) {
            return;
        }

        $this->notifyAdmins([
            'title' => __('Refund processed'),
            'body' => __(':amount was refunded to :customer.', [
                'amount' => number_format((float) $refund->amount, 2).' '.strtoupper((string) $payment->currency),
                'customer' => $this->customerName($payment),
            ]),
            'icon' => 'ph-arrow-counter-clockwise',
            'type' => 'warning',
            'url' => $this->routeOrNull('admin.refunds.show', $refund),
        ], 'admin.refund_processed', 'refund:'.$refund->id.':processed');
    }

    public function renderFailed(RenderJob $job): void
    {
        $job->loadMissing('user');

        $this->notifyAdmins([
            'title' => __('Render job failed'),
            'body' => __(':file failed for :customer.', [
                'file' => $job->displayName(),
                'customer' => $job->user?->name ?? __('Unknown user'),
            ]),
            'icon' => 'ph-warning-diamond',
            'type' => 'danger',
            'url' => $this->routeOrNull('admin.render-jobs.index', ['status' => 'failed', 'search' => $job->uuid]),
        ], 'admin.render_failed', 'render:'.$job->id.':failed');
    }

    public function supportTicketOpened(SupportTicket $ticket): void
    {
        $ticket->loadMissing('user');

        $this->notifyAdmins([
            'title' => __('New support ticket'),
            'body' => __(':customer opened ticket :reference.', [
                'customer' => $ticket->user?->name ?? __('Unknown user'),
                'reference' => $ticket->reference,
            ]),
            'icon' => 'ph-life-buoy',
            'type' => 'warning',
            'url' => $this->routeOrNull('admin.support-tickets.show', $ticket),
        ], 'admin.support_ticket_opened', 'support-ticket:'.$ticket->id.':opened');
    }

    public function supportUserReplied(SupportTicket $ticket): void
    {
        $ticket->loadMissing('user');

        $this->notifyAdmins([
            'title' => __('Support ticket reply'),
            'body' => __(':customer replied on ticket :reference.', [
                'customer' => $ticket->user?->name ?? __('Unknown user'),
                'reference' => $ticket->reference,
            ]),
            'icon' => 'ph-chat-circle-dots',
            'type' => 'info',
            'url' => $this->routeOrNull('admin.support-tickets.show', $ticket),
        ], 'admin.support_user_replied', 'support-ticket:'.$ticket->id.':reply:'.$ticket->last_reply_at?->timestamp);
    }

    /**
     * @param  array{title: string, body: string, icon?: string, url?: string, type?: string, key?: string}  $data
     */
    protected function notifyAdmins(array $data, string $type, ?string $key = null): int
    {
        $count = 0;

        foreach ($this->activeAdmins() as $admin) {
            if ($key) {
                $this->notifications->sendOnce($admin, $data, $type, 'admin:'.$admin->id.':'.$key);
            } else {
                $this->notifications->send($admin, $data, $type);
            }

            $count++;
        }

        return $count;
    }

    /**
     * @return Collection<int, Admin>
     */
    protected function activeAdmins(): Collection
    {
        return Admin::query()
            ->where('is_active', true)
            ->get();
    }

    protected function customerName(Payment $payment): string
    {
        $user = $payment->user;

        return $user?->name ?? $user?->email ?? __('Unknown customer');
    }

    protected function formatMoney(Payment $payment): string
    {
        return number_format((float) $payment->amount, 2).' '.strtoupper((string) $payment->currency);
    }

    protected function routeOrNull(string $name, mixed ...$parameters): ?string
    {
        $parameters = count($parameters) === 1 && is_array($parameters[0]) ? $parameters[0] : $parameters;

        return Route::has($name) ? route($name, $parameters) : null;
    }
}
