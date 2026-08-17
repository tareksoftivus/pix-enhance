<?php

namespace App\Modules\PricingPlan\Tables;

use App\Modules\PaymentGateways\Models\Payment;
use App\Modules\Shared\Support\Tables\TableAction;
use App\Modules\Shared\Support\Tables\TableColumn;
use App\Modules\Shared\Support\Tables\TableDefinition;
use Illuminate\Support\Facades\Route;

class SubscribersTable
{
    public static function make(): TableDefinition
    {
        return TableDefinition::make('subscribers')
            ->searchPlaceholder(__('Search by user name or email'))
            ->emptyMessage(__('No subscribers found.'))
            ->columns([
                TableColumn::view('user', __('User'), 'pricing-plans::admin.partials.subscriber-user'),
                TableColumn::view('plan', __('Plan'), 'pricing-plans::admin.partials.subscriber-plan'),
                TableColumn::view('amount', __('Credits'), 'pricing-plans::admin.partials.subscriber-credits'),
                TableColumn::view('source', __('Source'), 'pricing-plans::admin.partials.subscriber-source'),
                TableColumn::date('created_at', __('Subscribed'))
                    ->sortable()
                    ->cellClass('text-sm text-neutral-500'),
            ])
            ->actions([
                TableAction::link('view_user', fn ($transaction) => $transaction->user && Route::has('admin.users.show')
                    ? route('admin.users.show', $transaction->user)
                    : '', __('View User'))
                    ->icon('user')
                    ->visible(fn ($transaction) => (bool) $transaction->user && Route::has('admin.users.show')),
                TableAction::link('view_payment', fn ($transaction) => $transaction->reference instanceof Payment && Route::has('admin.payments.show')
                    ? route('admin.payments.show', $transaction->reference)
                    : '', __('View Payment'))
                    ->icon('receipt')
                    ->visible(fn ($transaction) => $transaction->reference instanceof Payment && Route::has('admin.payments.show')),
            ]);
    }
}
