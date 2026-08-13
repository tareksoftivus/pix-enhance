<?php

namespace App\Modules\Blog;

use App\Modules\Blog\Models\BlogCategory;
use App\Modules\Blog\Models\BlogPost;
use App\Modules\Blog\Policies\BlogCategoryPolicy;
use App\Modules\Blog\Policies\BlogPostPolicy;
use App\Modules\Shared\Support\BasePanelModule;
use App\Modules\Shared\Support\NavigationBuilder;

class Module extends BasePanelModule
{
    public function id(): string
    {
        return 'blog';
    }

    public function permissions(): array
    {
        return [
            'admin' => [
                'blog-posts.view' => 'View blog posts',
                'blog-posts.create' => 'Create blog posts',
                'blog-posts.edit' => 'Edit blog posts',
                'blog-posts.delete' => 'Delete blog posts',
                'blog-categories.view' => 'View blog categories',
                'blog-categories.create' => 'Create blog categories',
                'blog-categories.edit' => 'Edit blog categories',
                'blog-categories.delete' => 'Delete blog categories',
            ],
        ];
    }

    public function policies(): array
    {
        return [
            BlogPost::class => BlogPostPolicy::class,
            BlogCategory::class => BlogCategoryPolicy::class,
        ];
    }

    public function adminNavigation(NavigationBuilder $navigation): void
    {
        $navigation
            ->group('Management')
            ->item(label: 'Blog', route: 'admin.blog-posts.*')
            ->icon('ph-article')
            ->permission('blog-posts.view')
            ->children([
                ['label' => 'Posts', 'route' => 'admin.blog-posts.*', 'icon' => 'ph-article'],
                ['label' => 'Categories', 'route' => 'admin.blog-categories.*', 'icon' => 'ph-folders', 'permission' => 'blog-categories.view'],
            ])
            ->order(45);
    }
}
