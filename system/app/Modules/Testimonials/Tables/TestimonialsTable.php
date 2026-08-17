<?php

namespace App\Modules\Testimonials\Tables;

use App\Modules\Shared\Support\Tables\TableAction;
use App\Modules\Shared\Support\Tables\TableBulkAction;
use App\Modules\Shared\Support\Tables\TableColumn;
use App\Modules\Shared\Support\Tables\TableDefinition;

class TestimonialsTable
{
    public static function make(): TableDefinition
    {
        return TableDefinition::make('testimonials')
            ->emptyMessage('No testimonials found.')
            ->columns([
                TableColumn::select(),
                TableColumn::text('client_name', 'Client')
                    ->sortable()
                    ->link(fn ($testimonial) => route('admin.testimonials.edit', $testimonial))
                    ->cellClass('text-sm font-semibold text-neutral-900'),
                TableColumn::text('company_name', 'Company')
                    ->format(function ($value, $testimonial): string {
                        $company = $value ?: null;
                        $designation = $testimonial->designation ?: null;

                        if ($company !== null && $designation !== null) {
                            return "{$company} - {$designation}";
                        }

                        return $company ?? $designation ?? '-';
                    })
                    ->cellClass('text-sm text-neutral-600'),
                TableColumn::number('rating', 'Rating')
                    ->format(fn ($value) => $value ? "{$value}/5" : '-')
                    ->cellClass('text-sm text-neutral-600'),
                TableColumn::booleanBadge('active', 'Status'),
                TableColumn::date('created_at', 'Created')
                    ->sortable()
                    ->cellClass('text-sm text-neutral-600'),
            ])
            ->actions([
                TableAction::link('edit', fn ($testimonial) => route('admin.testimonials.edit', $testimonial), 'Edit')
                    ->icon('pencil-simple'),
                TableAction::toggleStatus(fn ($testimonial) => route('admin.testimonials.toggle-status', $testimonial), 'active')
                    ->icon('power')
                    ->activeLabel('Deactivate')
                    ->inactiveLabel('Activate')
                    ->confirmTitle(fn ($testimonial) => $testimonial->active ? __('Deactivate Testimonial?') : __('Activate Testimonial?'))
                    ->confirmMessage(fn ($testimonial) => $testimonial->active
                        ? __('Are you sure you want to deactivate \':name\'?', ['name' => $testimonial->client_name])
                        : __('Are you sure you want to activate \':name\'?', ['name' => $testimonial->client_name])),
                TableAction::delete(href: fn ($testimonial) => route('admin.testimonials.destroy', $testimonial))
                    ->icon('trash')
                    ->confirmTitle(__('Delete Testimonial?'))
                    ->confirmMessage(fn ($testimonial) => __('Are you sure you want to delete \':name\'? This action cannot be undone.', ['name' => $testimonial->client_name])),
            ])
            ->bulkActions(
                TableBulkAction::make('testimonials')
                    ->deleteAction(route('admin.testimonials.bulk-delete'))
            );
    }
}
