<div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
    <x-ui.kpi-card
        :title="__('Net Revenue')"
        :value="'$' . number_format($stats['net_revenue'], 2)"
        icon="ph-trend-up"
        color="primary"
        :change="$stats['completion_rate'] . '% ' . __('paid')"
        changeType="primary"
    />
    <x-ui.kpi-card
        :title="__('Completed Payments')"
        :value="number_format($stats['completed_payments'])"
        icon="ph-check-circle"
        color="success"
        :change="__('Collected')"
        changeType="success"
    />
    <x-ui.kpi-card
        :title="__('Pending Payments')"
        :value="number_format($stats['pending_payments'])"
        icon="ph-clock"
        color="warning"
        :change="__('Awaiting')"
        changeType="warning"
    />
    <x-ui.kpi-card
        :title="__('Refunded')"
        :value="'$' . number_format($stats['refund_amount'], 2)"
        icon="ph-arrow-counter-clockwise"
        color="error"
        :change="__('Completed')"
        changeType="danger"
    />
</div>
