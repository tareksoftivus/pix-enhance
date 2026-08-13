<?php

namespace App\Modules\Blog\Tables;

use App\Modules\Blog\Models\BlogPost;
use App\Modules\Shared\Support\Tables\TableAction;
use App\Modules\Shared\Support\Tables\TableColumn;
use App\Modules\Shared\Support\Tables\TableDefinition;

class BlogPostsTable
{
    public static function make(): TableDefinition
    {
        return TableDefinition::make('blog-posts')
            ->emptyMessage(__('No posts found.'))
            ->searchPlaceholder(__('Search posts...'))
            ->columns([
                TableColumn::text('title', 'Title')
                    ->sortable()
                    ->link(fn (BlogPost $post) => route('admin.blog-posts.edit', $post))
                    ->cellClass('text-sm font-semibold text-neutral-900'),
                TableColumn::text('category', 'Category')
                    ->value(fn (BlogPost $post) => $post->category?->name ?? '—')
                    ->cellClass('text-sm text-neutral-600'),
                TableColumn::badge('status', 'Status')
                    ->sortable()
                    ->meta(['badge_map' => BlogPost::statuses()]),
                TableColumn::text('author', 'Author')
                    ->value(fn (BlogPost $post) => $post->author?->name ?? '—')
                    ->cellClass('text-sm text-neutral-600'),
                TableColumn::date('published_at', 'Published')
                    ->sortable()
                    ->cellClass('text-sm text-neutral-500'),
            ])
            ->actions([
                TableAction::link('edit', fn (BlogPost $post) => route('admin.blog-posts.edit', $post), 'Edit')
                    ->icon('pencil-simple'),
                TableAction::delete(href: fn (BlogPost $post) => route('admin.blog-posts.destroy', $post))
                    ->icon('trash')
                    ->confirmTitle(__('Delete Post?'))
                    ->confirmMessage(fn (BlogPost $post) => __('Are you sure you want to delete \':title\'? This cannot be undone.', ['title' => $post->title])),
            ]);
    }
}
