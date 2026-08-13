<?php

namespace App\Modules\Blog\Policies;

use App\Modules\Blog\Models\BlogCategory;
use Illuminate\Contracts\Auth\Authenticatable;

class BlogCategoryPolicy
{
    public function viewAny(Authenticatable $user): bool
    {
        return $user->can('blog-categories.view');
    }

    public function view(Authenticatable $user, BlogCategory $category): bool
    {
        return $user->can('blog-categories.view');
    }

    public function create(Authenticatable $user): bool
    {
        return $user->can('blog-categories.create');
    }

    public function update(Authenticatable $user, BlogCategory $category): bool
    {
        return $user->can('blog-categories.edit');
    }

    public function delete(Authenticatable $user, BlogCategory $category): bool
    {
        return $user->can('blog-categories.delete');
    }
}
