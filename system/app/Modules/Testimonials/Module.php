<?php

namespace App\Modules\Testimonials;

use App\Modules\Shared\Support\BasePanelModule;
use App\Modules\Shared\Support\NavigationBuilder;
use App\Modules\Testimonials\Models\Testimonial;
use App\Modules\Testimonials\Policies\TestimonialPolicy;

class Module extends BasePanelModule
{
    public function id(): string
    {
        return 'testimonials';
    }

    public function permissions(): array
    {
        return [
            'admin' => [
                'testimonials.view' => 'View Testimonials',
                'testimonials.create' => 'Create Testimonials',
                'testimonials.edit' => 'Edit Testimonials',
                'testimonials.delete' => 'Delete Testimonials',
            ],
        ];
    }

    public function policies(): array
    {
        return [
            Testimonial::class => TestimonialPolicy::class,
        ];
    }

    public function adminNavigation(NavigationBuilder $navigation): void
    {
        $navigation
            ->group('Content')
            ->item('Testimonials', 'admin.testimonials.*', 'ph-chat-circle-text', 'testimonials.view', 51);
    }
}
