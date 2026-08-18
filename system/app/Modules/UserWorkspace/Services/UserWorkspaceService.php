<?php

namespace App\Modules\UserWorkspace\Services;

use App\Models\User;
use App\Modules\Credits\Models\CreditTransaction;
use App\Modules\Credits\Services\CreditService;
use App\Modules\LoginActivity\Models\LoginActivity;
use App\Modules\PaymentGateways\Models\Payment;
use App\Modules\RenderJobs\Models\RenderJob;
use App\Modules\RenderJobs\Services\RenderJobService;
use App\Modules\Support\Models\SupportTicket;
use App\Modules\Support\Models\SupportTicketReply;
use App\Modules\UserWorkspace\Models\UserWorkspacePreference;
use Carbon\CarbonInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class UserWorkspaceService
{
    /**
     * @var array<int, string>
     */
    protected array $historyTypes = ['render', 'billing', 'support', 'security', 'account'];

    /**
     * @return array<string, mixed>
     */
    public function dashboardFor(User $user): array
    {
        $preferences = $this->preferencesFor($user);
        $creditSummary = $this->creditSummaryFor($user);
        $renderSummary = $this->renderSummaryFor($user);

        return [
            'stats' => [
                'images_enhanced' => $renderSummary['completed'],
                'credits_remaining' => $creditSummary['available'],
                'average_render' => $this->formatDuration($renderSummary['average_ms']),
                'storage_used' => $this->formatBytes($renderSummary['storage_bytes']),
            ],
            'recent_enhancements' => $this->recentRenderCardsFor($user),
            'preferences' => $preferences,
            'quick_settings' => [
                'model' => Arr::get($preferences, 'render_defaults.default_model'),
                'scale' => Arr::get($preferences, 'render_defaults.default_scale'),
                'format' => Arr::get($preferences, 'render_defaults.default_format'),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function historyFor(User $user, array $filters = [], int $perPage = 12): array
    {
        $type = $this->normalizeHistoryType($filters['type'] ?? null);
        $search = is_string($filters['search'] ?? null) ? trim($filters['search']) : '';

        $allEvents = $this->historyEventsFor($user);
        $searchedEvents = $this->searchHistoryEvents($allEvents, $search);
        $visibleEvents = $type === 'all'
            ? $searchedEvents
            : $searchedEvents->where('type', $type)->values();

        return [
            'events' => $this->paginateHistoryEvents($visibleEvents, $filters, $perPage),
            'counts' => $this->historyCounts($searchedEvents),
            'stats' => $this->historyCounts($allEvents),
            'filters' => [
                'type' => $type,
                'search' => $search,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function preferencesFor(User $user): array
    {
        $preference = UserWorkspacePreference::query()
            ->where('user_id', $user->id)
            ->first();

        return $this->normalizePreferences($preference);
    }

    /**
     * @param  array<string, bool>  $preferences
     */
    public function updateNotificationPreferences(User $user, array $preferences): UserWorkspacePreference
    {
        $workspacePreference = $this->firstOrCreatePreference($user);

        $workspacePreference->forceFill([
            'notification_preferences' => array_merge(
                $this->defaultNotificationPreferences(),
                Arr::only($preferences, array_keys($this->defaultNotificationPreferences()))
            ),
            'desktop_notifications_enabled' => (bool) ($preferences['desktop_notifications_enabled'] ?? false),
            'completion_sound_enabled' => (bool) ($preferences['completion_sound_enabled'] ?? false),
        ])->save();

        return $workspacePreference;
    }

    /**
     * @param  array<string, mixed>  $defaults
     */
    public function updateRenderDefaults(User $user, array $defaults): UserWorkspacePreference
    {
        $workspacePreference = $this->firstOrCreatePreference($user);

        $workspacePreference->forceFill([
            'render_defaults' => array_merge(
                $this->defaultRenderDefaults(),
                Arr::only($defaults, array_keys($this->defaultRenderDefaults()))
            ),
            'source_retention_days' => (int) ($defaults['source_retention_days'] ?? 7),
        ])->save();

        return $workspacePreference;
    }

    public function firstOrCreatePreference(User $user): UserWorkspacePreference
    {
        return UserWorkspacePreference::query()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'notification_preferences' => $this->defaultNotificationPreferences(),
                'render_defaults' => $this->defaultRenderDefaults(),
                'source_retention_days' => 7,
                'desktop_notifications_enabled' => false,
                'completion_sound_enabled' => false,
            ]
        );
    }

    /**
     * @return array<string, bool>
     */
    public function defaultNotificationPreferences(): array
    {
        return [
            'render_finished' => true,
            'credits_low' => true,
            'weekly_summary' => false,
            'product_news' => false,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function defaultRenderDefaults(): array
    {
        return [
            'default_model' => 'auto',
            'default_scale' => 4,
            'default_format' => 'png',
            'face_restoration' => true,
            'auto_download' => false,
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function historyEventsFor(User $user): Collection
    {
        return collect()
            ->merge($this->accountHistoryEvents($user))
            ->merge($this->renderHistoryEvents($user))
            ->merge($this->creditHistoryEvents($user))
            ->merge($this->securityHistoryEvents($user))
            ->merge($this->supportHistoryEvents($user))
            ->merge($this->billingHistoryEvents($user))
            ->sortByDesc(fn (array $event): int => $event['occurred_at']?->getTimestamp() ?? 0)
            ->values();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function renderHistoryEvents(User $user): Collection
    {
        if (! class_exists(RenderJob::class) || ! $this->tableExists('render_jobs')) {
            return collect();
        }

        return RenderJob::query()
            ->forUser((int) $user->getKey())
            ->latest('created_at')
            ->limit(100)
            ->get()
            ->map(function (RenderJob $job): array {
                $status = RenderJob::statuses()[$job->status]['label'] ?? Str::headline($job->status);

                return $this->historyEvent(
                    type: 'render',
                    icon: RenderJob::statuses()[$job->status]['icon'] ?? 'sparkles',
                    title: match ($job->status) {
                        'completed' => __('Render completed'),
                        'failed' => __('Render failed'),
                        'cancelled' => __('Render cancelled'),
                        'processing' => __('Render processing'),
                        default => __('Render queued'),
                    },
                    detail: $job->source_name.' · '.$job->toolLabel(),
                    occurredAt: $job->completed_at ?? $job->failed_at ?? $job->cancelled_at ?? $job->created_at,
                    meta: $status.' · '.number_format($job->credits_cost).' '.__('credits'),
                    url: $this->routeUrl('user.render-jobs.show', [$job])
                );
            });
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function creditHistoryEvents(User $user): Collection
    {
        if (! class_exists(CreditTransaction::class) || ! $this->tableExists('credit_transactions')) {
            return collect();
        }

        return CreditTransaction::query()
            ->forUser((int) $user->getKey())
            ->latest('created_at')
            ->limit(100)
            ->get()
            ->map(function (CreditTransaction $transaction): array {
                $isCredit = $transaction->amount > 0;
                $metadata = $transaction->metadata ?? [];

                $title = match ($transaction->reason) {
                    'signup_bonus' => __('Signup credits added'),
                    'credit_pack_purchase' => __('Credit pack added'),
                    'pricing_plan_purchase' => __('Plan credits added'),
                    'render_spend' => __('Credits spent on render'),
                    'render_refund' => __('Render credits returned'),
                    'payment_refund' => __('Credits removed after refund'),
                    'admin_adjustment' => $isCredit ? __('Credits added by admin') : __('Credits removed by admin'),
                    default => $isCredit ? __('Credits added') : __('Credits spent'),
                };

                $detail = $metadata['credit_pack_name']
                    ?? $metadata['pricing_plan_name']
                    ?? $metadata['note']
                    ?? Str::headline((string) $transaction->reason);

                return $this->historyEvent(
                    type: 'billing',
                    icon: $isCredit ? 'coins' : 'zap',
                    title: $title,
                    detail: (string) $detail,
                    occurredAt: $transaction->created_at,
                    meta: ($isCredit ? '+' : '').number_format($transaction->amount).' '.__('credits'),
                    url: $this->routeUrl('user.billing')
                );
            });
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function accountHistoryEvents(User $user): Collection
    {
        $events = collect([
            $this->historyEvent(
                type: 'account',
                icon: 'user',
                title: __('Account created'),
                detail: __('Workspace opened for :email', ['email' => $user->email]),
                occurredAt: $user->created_at,
                url: $this->routeUrl('user.settings')
            ),
        ]);

        if ($user->email_verified_at) {
            $events->push($this->historyEvent(
                type: 'account',
                icon: 'mail',
                title: __('Email address verified'),
                detail: $user->email,
                occurredAt: $user->email_verified_at,
                url: $this->routeUrl('user.settings')
            ));
        }

        if ($user->phone_verified_at) {
            $events->push($this->historyEvent(
                type: 'account',
                icon: 'badge-check',
                title: __('Phone number verified'),
                detail: $user->phone ?: __('Phone verification completed'),
                occurredAt: $user->phone_verified_at,
                url: $this->routeUrl('user.settings')
            ));
        }

        return $events->filter(fn (array $event): bool => $event['occurred_at'] instanceof CarbonInterface)->values();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function securityHistoryEvents(User $user): Collection
    {
        if (! $this->tableExists('login_activities')) {
            return collect();
        }

        return LoginActivity::query()
            ->forUser($user->getMorphClass(), (int) $user->getKey())
            ->latest('created_at')
            ->limit(100)
            ->get()
            ->map(function (LoginActivity $activity): array {
                $title = match ($activity->event) {
                    'logout' => __('Signed out'),
                    'impersonate_start' => __('Staff session started'),
                    'impersonate_stop' => __('Staff session ended'),
                    default => __('Signed in'),
                };

                $icon = match ($activity->event) {
                    'logout' => 'log-out',
                    'impersonate_start', 'impersonate_stop' => 'shield-alert',
                    default => 'shield-check',
                };

                $detail = collect([
                    $activity->browser,
                    $activity->platform,
                    $activity->device,
                    $activity->ip_address,
                ])->filter()->implode(' · ');

                return $this->historyEvent(
                    type: 'security',
                    icon: $icon,
                    title: $title,
                    detail: $detail ?: __('Device details unavailable'),
                    occurredAt: $activity->created_at,
                    meta: Str::headline((string) $activity->event),
                    url: $this->routeUrl('user.settings')
                );
            });
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function supportHistoryEvents(User $user): Collection
    {
        if (! $this->tableExists('support_tickets')) {
            return collect();
        }

        $tickets = SupportTicket::query()
            ->forUser((int) $user->getKey())
            ->latest('updated_at')
            ->limit(100)
            ->get();

        $events = $tickets->flatMap(function (SupportTicket $ticket): array {
            $ticketUrl = $this->routeUrl('user.support-tickets.show', [$ticket]);
            $status = SupportTicket::statuses()[$ticket->status]['label'] ?? Str::headline((string) $ticket->status);
            $priority = SupportTicket::priorities()[$ticket->priority]['label'] ?? Str::headline((string) $ticket->priority);

            $items = [
                $this->historyEvent(
                    type: 'support',
                    icon: 'life-buoy',
                    title: __('Opened ticket :reference', ['reference' => $ticket->reference]),
                    detail: __(':subject · :priority priority', [
                        'subject' => $ticket->subject,
                        'priority' => Str::lower($priority),
                    ]),
                    occurredAt: $ticket->created_at,
                    meta: $status,
                    url: $ticketUrl
                ),
            ];

            if (in_array($ticket->status, ['resolved', 'closed'], true) && $ticket->updated_at?->gt($ticket->created_at)) {
                $items[] = $this->historyEvent(
                    type: 'support',
                    icon: $ticket->status === 'resolved' ? 'circle-check' : 'folder',
                    title: __('Ticket :reference :status', [
                        'reference' => $ticket->reference,
                        'status' => Str::lower($status),
                    ]),
                    detail: $ticket->subject,
                    occurredAt: $ticket->updated_at,
                    meta: $status,
                    url: $ticketUrl
                );
            }

            return $items;
        });

        if (! $this->tableExists('support_ticket_replies') || $tickets->isEmpty()) {
            return $events->values();
        }

        $replyEvents = SupportTicketReply::query()
            ->with('ticket')
            ->whereIn('support_ticket_id', $tickets->pluck('id'))
            ->latest('created_at')
            ->limit(100)
            ->get()
            ->map(function (SupportTicketReply $reply): array {
                $ticket = $reply->ticket;
                $fromStaff = $reply->isFromStaff();

                return $this->historyEvent(
                    type: 'support',
                    icon: $fromStaff ? 'reply' : 'message-circle',
                    title: $fromStaff
                        ? __('Staff replied to ticket :reference', ['reference' => $ticket?->reference])
                        : __('You replied to ticket :reference', ['reference' => $ticket?->reference]),
                    detail: Str::limit(strip_tags($reply->message), 100),
                    occurredAt: $reply->created_at,
                    meta: $ticket ? (SupportTicket::statuses()[$ticket->status]['label'] ?? Str::headline((string) $ticket->status)) : null,
                    url: $ticket ? $this->routeUrl('user.support-tickets.show', [$ticket]) : null
                );
            });

        return $events->merge($replyEvents)->values();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function billingHistoryEvents(User $user): Collection
    {
        if (! $this->tableExists('payments')) {
            return collect();
        }

        return Payment::query()
            ->with('refunds')
            ->where('user_type', $user->getMorphClass())
            ->where('user_id', $user->getKey())
            ->latest('created_at')
            ->limit(100)
            ->get()
            ->flatMap(function (Payment $payment): array {
                $paymentUrl = $this->routeUrl('user.billing');
                $status = Str::headline((string) $payment->status);
                $gateway = Str::headline((string) $payment->gateway);
                $method = $payment->payment_method ? Str::headline($payment->payment_method) : __('Payment method unavailable');

                $events = [
                    $this->historyEvent(
                        type: 'billing',
                        icon: match ($payment->status) {
                            'completed' => 'credit-card',
                            'failed' => 'circle-alert',
                            'refunded' => 'refresh-cw',
                            default => 'file-text',
                        },
                        title: match ($payment->status) {
                            'completed' => __('Payment completed'),
                            'failed' => __('Payment failed'),
                            'refunded' => __('Payment refunded'),
                            default => __('Payment started'),
                        },
                        detail: $payment->description ?: __(':gateway · :method', ['gateway' => $gateway, 'method' => $method]),
                        occurredAt: $payment->paid_at ?? $payment->created_at,
                        meta: $this->formatMoney((float) $payment->amount, (string) $payment->currency),
                        url: $paymentUrl
                    ),
                ];

                foreach ($payment->refunds as $refund) {
                    $events[] = $this->historyEvent(
                        type: 'billing',
                        icon: 'refresh-cw',
                        title: __('Refund :status', ['status' => Str::lower(Str::headline((string) $refund->status))]),
                        detail: $refund->reason ?: ($payment->description ?: __('Payment refund')),
                        occurredAt: $refund->created_at,
                        meta: $this->formatMoney((float) $refund->amount, (string) $payment->currency),
                        url: $paymentUrl
                    );
                }

                return $events;
            })
            ->values();
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $events
     * @return Collection<int, array<string, mixed>>
     */
    protected function searchHistoryEvents(Collection $events, string $search): Collection
    {
        if ($search === '') {
            return $events;
        }

        $needle = Str::lower($search);

        return $events
            ->filter(function (array $event) use ($needle): bool {
                $haystack = Str::lower(implode(' ', array_filter([
                    $event['type'] ?? '',
                    $event['title'] ?? '',
                    $event['detail'] ?? '',
                    $event['meta'] ?? '',
                ])));

                return Str::contains($haystack, $needle);
            })
            ->values();
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $events
     * @param  array<string, mixed>  $filters
     */
    protected function paginateHistoryEvents(Collection $events, array $filters, int $perPage): LengthAwarePaginator
    {
        $page = max(1, (int) ($filters['page'] ?? LengthAwarePaginator::resolveCurrentPage()));

        return new LengthAwarePaginator(
            items: $events->forPage($page, $perPage)->values(),
            total: $events->count(),
            perPage: $perPage,
            currentPage: $page,
            options: [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => request()->query(),
            ],
        );
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $events
     * @return array<string, int>
     */
    protected function historyCounts(Collection $events): array
    {
        $counts = array_fill_keys(array_merge(['all'], $this->historyTypes), 0);

        foreach ($events->countBy('type') as $type => $count) {
            $counts[$type] = $count;
        }

        $counts['all'] = $events->count();

        return $counts;
    }

    protected function normalizeHistoryType(mixed $type): string
    {
        return is_string($type) && in_array($type, $this->historyTypes, true) ? $type : 'all';
    }

    /**
     * @return array<string, mixed>
     */
    protected function historyEvent(
        string $type,
        string $icon,
        string $title,
        string $detail,
        ?CarbonInterface $occurredAt,
        ?string $meta = null,
        ?string $url = null
    ): array {
        return [
            'type' => $type,
            'icon' => $icon,
            'title' => $title,
            'detail' => $detail,
            'when' => $occurredAt?->diffForHumans() ?? __('Date unavailable'),
            'meta' => $meta,
            'url' => $url,
            'occurred_at' => $occurredAt,
            'date_title' => $occurredAt?->format('M j, Y g:i A'),
        ];
    }

    /**
     * @param  array<int, mixed>  $parameters
     */
    protected function routeUrl(string $name, array $parameters = []): ?string
    {
        return Route::has($name) ? route($name, $parameters) : null;
    }

    protected function tableExists(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (\Throwable) {
            return false;
        }
    }

    protected function formatMoney(float $amount, string $currency): string
    {
        return strtoupper($currency).' '.number_format($amount, 2);
    }

    /**
     * @return array{available: int}
     */
    protected function creditSummaryFor(User $user): array
    {
        if (! class_exists(CreditService::class) || ! $this->tableExists('credit_wallets')) {
            return ['available' => 0];
        }

        return [
            'available' => app(CreditService::class)->summaryFor($user)['available'],
        ];
    }

    /**
     * @return array{completed: int, storage_bytes: int, average_ms: int}
     */
    protected function renderSummaryFor(User $user): array
    {
        if (! class_exists(RenderJobService::class) || ! $this->tableExists('render_jobs')) {
            return ['completed' => 0, 'storage_bytes' => 0, 'average_ms' => 0];
        }

        $summary = app(RenderJobService::class)->summaryFor($user);

        return [
            'completed' => (int) $summary['completed'],
            'storage_bytes' => (int) $summary['storage_bytes'],
            'average_ms' => (int) $summary['average_ms'],
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function recentRenderCardsFor(User $user): Collection
    {
        if (! class_exists(RenderJobService::class) || ! $this->tableExists('render_jobs')) {
            return collect();
        }

        return app(RenderJobService::class)->recentCardsFor($user, null, 6);
    }

    /**
     * @return array<string, mixed>
     */
    protected function normalizePreferences(?UserWorkspacePreference $preference): array
    {
        return [
            'notifications' => array_merge(
                $this->defaultNotificationPreferences(),
                $preference?->notification_preferences ?? []
            ),
            'render_defaults' => array_merge(
                $this->defaultRenderDefaults(),
                $preference?->render_defaults ?? []
            ),
            'source_retention_days' => $preference?->source_retention_days ?? 7,
            'desktop_notifications_enabled' => $preference?->desktop_notifications_enabled ?? false,
            'completion_sound_enabled' => $preference?->completion_sound_enabled ?? false,
        ];
    }

    protected function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        }

        $units = ['KB', 'MB', 'GB', 'TB'];
        $value = $bytes / 1024;

        foreach ($units as $unit) {
            if ($value < 1024) {
                return number_format($value, $value >= 10 ? 0 : 1).' '.$unit;
            }

            $value /= 1024;
        }

        return number_format($value, 1).' PB';
    }

    protected function formatDuration(int $milliseconds): string
    {
        if ($milliseconds <= 0) {
            return '0s';
        }

        return $milliseconds < 1000
            ? $milliseconds.'ms'
            : number_format($milliseconds / 1000, 1).'s';
    }
}
