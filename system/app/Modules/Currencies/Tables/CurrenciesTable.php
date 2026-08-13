<?php

namespace App\Modules\Currencies\Tables;

use App\Modules\Shared\Support\Tables\TableAction;
use App\Modules\Shared\Support\Tables\TableColumn;
use App\Modules\Shared\Support\Tables\TableDefinition;

class CurrenciesTable
{
    public static function make(): TableDefinition
    {
        return TableDefinition::make('currencies')
            ->emptyMessage('No currencies found.')
            ->columns([
                TableColumn::text('code', 'Code')
                    ->sortable()
                    ->link(fn ($currency) => route('admin.currencies.edit', $currency))
                    ->cellClass('text-sm font-semibold text-neutral-900'),
                TableColumn::text('name', 'Name')
                    ->sortable()
                    ->cellClass('text-sm text-neutral-900'),
                TableColumn::text('symbol', 'Symbol')
                    ->cellClass('text-sm text-neutral-900'),
                TableColumn::number('exchange_rate', 'Exchange Rate')
                    ->sortable()
                    ->meta(['decimals' => 6])
                    ->cellClass('text-sm text-neutral-600'),
                TableColumn::booleanBadge('is_active', 'Status'),
            ])
            ->actions([
                TableAction::link('edit', fn ($currency) => route('admin.currencies.edit', $currency), 'Edit')
                    ->icon('pencil-simple'),
                TableAction::delete(href: fn ($currency) => route('admin.currencies.destroy', $currency))
                    ->icon('trash')
                    ->confirmTitle(__('Delete Currency?'))
                    ->confirmMessage(fn ($currency) => __('Are you sure you want to delete \':name\'? This action cannot be undone.', ['name' => $currency->name])),
                TableAction::toggleStatus(fn ($currency) => route('admin.currencies.toggle-status', $currency))
                    ->icon('power')
                    ->activeLabel('Deactivate')
                    ->inactiveLabel('Activate')
                    ->confirmTitle(fn ($currency) => $currency->is_active ? __('Deactivate Currency?') : __('Activate Currency?'))
                    ->confirmMessage(fn ($currency) => $currency->is_active
                        ? __('Are you sure you want to deactivate \':name\'? This will make it unavailable for transactions.', ['name' => $currency->name])
                        : __('Are you sure you want to activate \':name\'? This will make it available for transactions.', ['name' => $currency->name])),
            ]);
    }
}
