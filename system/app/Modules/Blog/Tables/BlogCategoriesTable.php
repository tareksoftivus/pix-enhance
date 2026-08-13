<?php

namespace App\Modules\Blog\Tables;

use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Shared\Support\Tables\TableAction;
use App\Modules\Shared\Support\Tables\TableColumn;
use App\Modules\Shared\Support\Tables\TableDefinition;

class BlogCategoriesTable
{
    public static function make(): TableDefinition
    {
        return TableDefinition::make('blog-categories')
            ->emptyMessage(__('No categories found.'))
            ->searchPlaceholder(__('Search categories...'))
            ->columns([
                TableColumn::text('name', 'Name')
                    ->sortable()
                    ->link(fn (BlogCategory $category) => route('admin.blog-categories.edit', $category))
                    ->cellClass('text-sm font-semibold text-neutral-900'),
                TableColumn::text('slug', 'Slug')
                    ->cellClass('text-sm text-neutral-500'),
                TableColumn::number('posts_count', 'Posts')
                    ->value(fn (BlogCategory $category) => $category->posts()->count())
                    ->cellClass('text-sm text-neutral-600'),
                TableColumn::booleanBadge('is_active', 'Status'),
            ])
            ->actions([
                TableAction::link('edit', fn (BlogCategory $category) => route('admin.blog-categories.edit', $category), 'Edit')
                    ->icon('pencil-simple'),
                TableAction::toggleStatus(fn (BlogCategory $category) => route('admin.blog-categories.toggle-status', $category))
                    ->icon('power')
                    ->activeLabel('Deactivate')
                    ->inactiveLabel('Activate'),
                TableAction::delete(href: fn (BlogCategory $category) => route('admin.blog-categories.destroy', $category))
                    ->icon('trash')
                    ->confirmTitle(__('Delete Category?'))
                    ->confirmMessage(fn (BlogCategory $category) => __('Are you sure you want to delete \':name\'? Posts in it will become uncategorized.', ['name' => $category->name])),
            ]);
    }
}
