<?php

namespace App\Modules\Testimonials\Providers;

use App\Modules\Shared\Support\BasePanelModuleProvider;
use App\Modules\Testimonials\Services\TestimonialsService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

class TestimonialsServiceProvider extends BasePanelModuleProvider
{
    public function register(): void
    {
        //
    }

    protected function bootModule(array $module): void
    {
        View::composer('frontend.themes.enhance.sections.testimonials', function ($view): void {
            $testimonials = collect();

            if (Schema::hasTable('testimonials')) {
                $testimonials = app(TestimonialsService::class)->activeTestimonials();
            }

            $view->with('testimonials', $testimonials);
        });
    }
}
