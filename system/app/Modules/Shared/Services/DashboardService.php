<?php

namespace App\Modules\Shared\Services;

use App\Models\Admin;
use App\Models\User;
use App\Modules\AuditLog\Models\AuditLog;
use App\Modules\PaymentGateways\Models\Payment;
use App\Modules\PaymentGateways\Models\Refund;
use App\Modules\Shared\Contracts\DashboardWidget;
use App\Services\WidgetRegistry;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getAdminStats(): array
    {
        $completedPayments = Payment::query()->completed()->count();
        $pendingPayments = Payment::query()->where('status', 'pending')->count();
        $totalPayments = Payment::query()->count();
        $completedRevenue = (float) Payment::query()->completed()->sum('amount');
        $completedRefunds = (float) Refund::query()->where('status', 'completed')->sum('amount');
        $netRevenue = max(0, $completedRevenue - $completedRefunds);

        return [
            'net_revenue' => $netRevenue,
            'completed_payments' => $completedPayments,
            'pending_payments' => $pendingPayments,
            'refund_amount' => $completedRefunds,
            'completion_rate' => $totalPayments > 0 ? (int) round(($completedPayments / $totalPayments) * 100) : 0,
        ];
    }

    public function getUserRoleDistribution(): array
    {
        return Admin::select('roles.name', DB::raw('count(*) as count'))
            ->join('model_has_roles', function ($join) {
                $join->on('admins.id', '=', 'model_has_roles.model_id')
                    ->where('model_has_roles.model_type', '=', Admin::class);
            })
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->groupBy('roles.name')
            ->pluck('count', 'name')
            ->toArray();
    }

    public function getRecentActivity(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return AuditLog::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getRecentUsers(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return User::with('roles')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function getRecentPayments(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return Payment::with('user')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Completed-payment revenue for the last $months months, oldest first.
     *
     * Returns every month in the window (including zero-revenue ones) so the
     * chart always renders a continuous series.
     *
     * @return array{labels: array<int, string>, data: array<int, float>}
     */
    public function getMonthlyRevenue(int $months = 6): array
    {
        $start = now()->startOfMonth()->subMonths($months - 1);

        // Bucket completed revenue by YYYY-MM in PHP so the query stays
        // database-agnostic (no vendor-specific date functions).
        $totals = Payment::query()
            ->completed()
            ->where('paid_at', '>=', $start)
            ->get(['amount', 'paid_at'])
            ->groupBy(fn (Payment $payment) => $payment->paid_at->format('Y-m'))
            ->map(fn ($group) => (float) $group->sum('amount'));

        $labels = [];
        $data = [];

        for ($i = 0; $i < $months; $i++) {
            $month = $start->copy()->addMonths($i);
            $labels[] = $month->format('M');
            $data[] = round((float) ($totals[$month->format('Y-m')] ?? 0), 2);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * @return array{completed_payments: int, total_payments: int, completion_rate: int, pending_payments: int}
     */
    public function getPaymentHealthMetrics(): array
    {
        $completedPayments = Payment::query()->completed()->count();
        $pendingPayments = Payment::query()->where('status', 'pending')->count();
        $failedPayments = Payment::query()->where('status', 'failed')->count();
        $totalPayments = Payment::query()->count();

        // Other statuses (e.g. refunded/cancelled) roll into "other" so the
        // donut segments always sum to the total.
        $otherPayments = max(0, $totalPayments - $completedPayments - $pendingPayments - $failedPayments);

        return [
            'completed_payments' => $completedPayments,
            'pending_payments' => $pendingPayments,
            'failed_payments' => $failedPayments,
            'other_payments' => $otherPayments,
            'total_payments' => $totalPayments,
            'completion_rate' => $totalPayments > 0 ? (int) round(($completedPayments / $totalPayments) * 100) : 0,
        ];
    }

    /**
     * Get rendered widgets for a specific panel.
     *
     * @return Collection<int, DashboardWidget>
     */
    public function getWidgetsForPanel(string $panel, mixed $user = null): Collection
    {
        return app(WidgetRegistry::class)->getForPanel($panel, $user);
    }
}
