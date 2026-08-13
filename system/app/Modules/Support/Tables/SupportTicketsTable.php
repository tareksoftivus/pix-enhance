<?php

namespace App\Modules\Support\Tables;

use App\Modules\Shared\Support\Tables\TableAction;
use App\Modules\Shared\Support\Tables\TableColumn;
use App\Modules\Shared\Support\Tables\TableDefinition;
use App\Modules\Support\Models\SupportTicket;

class SupportTicketsTable
{
    /**
     * Admin queue: every ticket, newest activity first.
     */
    public static function forAdmin(): TableDefinition
    {
        return static::base()
            ->columns([
                TableColumn::text('reference', 'Reference')
                    ->sortable()
                    ->link(fn (SupportTicket $ticket) => route('admin.support-tickets.show', $ticket))
                    ->cellClass('text-sm font-semibold text-neutral-900'),
                TableColumn::text('subject', 'Subject')
                    ->sortable()
                    ->cellClass('text-sm text-neutral-900'),
                TableColumn::text('user', 'User')
                    ->value(fn (SupportTicket $ticket) => $ticket->user?->name ?? __('Unknown'))
                    ->cellClass('text-sm text-neutral-600'),
                static::priorityColumn(),
                static::statusColumn(),
                TableColumn::date('last_reply_at', 'Last Activity')
                    ->sortable()
                    ->cellClass('text-sm text-neutral-500'),
            ])
            ->actions([
                TableAction::link('view', fn (SupportTicket $ticket) => route('admin.support-tickets.show', $ticket), 'View')
                    ->icon('eye'),
                TableAction::delete(href: fn (SupportTicket $ticket) => route('admin.support-tickets.destroy', $ticket))
                    ->icon('trash')
                    ->confirmTitle(__('Delete Ticket?'))
                    ->confirmMessage(fn (SupportTicket $ticket) => __('Are you sure you want to delete \':reference\'? This action cannot be undone.', ['reference' => $ticket->reference])),
            ]);
    }

    /**
     * User panel: only the signed-in user's own tickets.
     */
    public static function forUser(): TableDefinition
    {
        return static::base()
            ->columns([
                TableColumn::text('reference', 'Reference')
                    ->sortable()
                    ->link(fn (SupportTicket $ticket) => route('user.support-tickets.show', $ticket))
                    ->cellClass('text-sm font-semibold text-neutral-900'),
                TableColumn::text('subject', 'Subject')
                    ->sortable()
                    ->cellClass('text-sm text-neutral-900'),
                static::priorityColumn(),
                static::statusColumn(),
                TableColumn::date('last_reply_at', 'Last Activity')
                    ->sortable()
                    ->cellClass('text-sm text-neutral-500'),
            ])
            ->actions([
                TableAction::link('view', fn (SupportTicket $ticket) => route('user.support-tickets.show', $ticket), 'View')
                    ->icon('eye'),
            ]);
    }

    protected static function base(): TableDefinition
    {
        return TableDefinition::make('support-tickets')
            ->emptyMessage(__('No support tickets found.'))
            ->searchPlaceholder(__('Search tickets...'));
    }

    protected static function statusColumn(): TableColumn
    {
        return TableColumn::badge('status', 'Status')
            ->sortable()
            ->meta(['badge_map' => SupportTicket::statuses()]);
    }

    protected static function priorityColumn(): TableColumn
    {
        return TableColumn::badge('priority', 'Priority')
            ->sortable()
            ->meta(['badge_map' => SupportTicket::priorities()]);
    }
}
