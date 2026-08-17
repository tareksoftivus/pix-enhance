<?php

namespace App\Modules\PricingPlan\Tables;

use App\Modules\Shared\Support\Tables\TableAction;
use App\Modules\Shared\Support\Tables\TableBulkAction;
use App\Modules\Shared\Support\Tables\TableColumn;
use App\Modules\Shared\Support\Tables\TableDefinition;

class PricingPlansTable
{
    public static function make(): TableDefinition
    {
        return TableDefinition::make('pricing_plans')
            ->emptyMessage('No pricing plans found.')
            ->columns([
                TableColumn::select(),
                TableColumn::text('name', 'Name')
                    ->sortable()
                    ->link(fn ($plan) => route('admin.pricing-plans.edit', $plan))
                    ->cellClass('text-sm font-semibold text-neutral-900'),
                TableColumn::number('price_monthly', 'Monthly')
                    ->meta(['decimals' => 0])
                    ->cellClass('text-sm text-neutral-600'),
                TableColumn::number('price_yearly', 'Yearly')
                    ->meta(['decimals' => 0])
                    ->cellClass('text-sm text-neutral-600'),
                TableColumn::number('credits_monthly', 'Credits/mo')
                    ->meta(['decimals' => 0])
                    ->cellClass('text-sm text-neutral-600'),
                TableColumn::booleanBadge('is_featured', 'Featured'),
                TableColumn::booleanBadge('is_active', 'Status'),
            ])
            ->actions([
                TableAction::link('view', fn ($plan) => route('admin.pricing-plans.show', $plan), 'View')
                    ->icon('eye'),
                TableAction::link('edit', fn ($plan) => route('admin.pricing-plans.edit', $plan), 'Edit')
                    ->icon('pencil-simple'),
                TableAction::toggleStatus(fn ($plan) => route('admin.pricing-plans.toggle-status', $plan))
                    ->icon('power'),
                TableAction::delete(href: fn ($plan) => route('admin.pricing-plans.destroy', $plan))
                    ->icon('trash')
                    ->confirmTitle(__('Delete Plan?'))
                    ->confirmMessage(fn ($plan) => __('Are you sure you want to delete \':name\'?', ['name' => $plan->name])),
            ])
            ->bulkActions(
                TableBulkAction::make('pricing_plans')
                    ->deleteAction(route('admin.pricing-plans.bulk-delete'))
                    ->toggleAction(route('admin.pricing-plans.bulk-toggle-status'))
            );
    }
}
