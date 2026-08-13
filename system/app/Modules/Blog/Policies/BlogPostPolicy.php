<?php

namespace App\Modules\Blog\Policies;

use App\Modules\Blog\Models\BlogPost;
use Illuminate\Contracts\Auth\Authenticatable;

class BlogPostPolicy
{
    public function viewAny(Authenticatable $user): bool
    {
        return $user->can('blog-posts.view');
    }

    public function view(Authenticatable $user, BlogPost $post): bool
    {
        return $user->can('blog-posts.view');
    }

    public function create(Authenticatable $user): bool
    {
        return $user->can('blog-posts.create');
    }

    public function update(Authenticatable $user, BlogPost $post): bool
    {
        return $user->can('blog-posts.edit');
    }

    public function delete(Authenticatable $user, BlogPost $post): bool
    {
        return $user->can('blog-posts.delete');
    }
}
