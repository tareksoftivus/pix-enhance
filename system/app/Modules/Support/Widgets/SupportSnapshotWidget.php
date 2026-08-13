<?php

namespace App\Modules\Support\Widgets;

use App\Modules\Shared\Widgets\BaseWidget;
use App\Modules\Support\Models\SupportTicket;

class SupportSnapshotWidget extends BaseWidget
{
    public function id(): string
    {
        return 'admin-support-snapshot';
    }

    public function title(): string
    {
        return __('Support Tickets');
    }

    public function render(): string
    {
        $counts = [
            'open' => SupportTicket::query()->where('status', 'open')->count(),
            'pending' => SupportTicket::query()->where('status', 'pending')->count(),
            'urgent' => SupportTicket::query()
                ->where('priority', 'urgent')
                ->whereNotIn('status', ['resolved', 'closed'])
                ->count(),
        ];

        $latestTickets = SupportTicket::query()
            ->with('user')
            ->whereNotIn('status', ['resolved', 'closed'])
            ->orderByDesc('last_reply_at')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return $this->view('support::widgets.snapshot', compact('counts', 'latestTickets'));
    }

    public function position(): int
    {
        return 62;
    }

    public function width(): string
    {
        return 'half';
    }
}
