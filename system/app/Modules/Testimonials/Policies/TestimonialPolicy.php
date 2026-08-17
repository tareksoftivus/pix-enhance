<?php

namespace App\Modules\Testimonials\Policies;

use App\Modules\Testimonials\Models\Testimonial;
use Illuminate\Contracts\Auth\Authenticatable;

class TestimonialPolicy
{
    public function viewAny(Authenticatable $user): bool
    {
        return $user->can('testimonials.view');
    }

    public function view(Authenticatable $user, Testimonial $testimonial): bool
    {
        return $user->can('testimonials.view');
    }

    public function create(Authenticatable $user): bool
    {
        return $user->can('testimonials.create');
    }

    public function update(Authenticatable $user, Testimonial $testimonial): bool
    {
        return $user->can('testimonials.edit');
    }

    public function delete(Authenticatable $user, Testimonial $testimonial): bool
    {
        return $user->can('testimonials.delete');
    }
}
