<?php

namespace App\Modules\PricingPlan\Providers;

use App\Modules\PricingPlan\Services\PricingPlanService;
use App\Modules\Shared\Support\BasePanelModuleProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

class PricingPlanServiceProvider extends BasePanelModuleProvider
{
    public function register(): void
    {
        //
    }

    protected function bootModule(array $module): void
    {
        View::composer([
            'frontend.themes.enhance.sections.pricing',
            'frontend.themes.enhance.sections.pricing_plans',
            'frontend.themes.enhance.sections.pricing_compare',
        ], function ($view): void {
            $plans = collect();

            if (Schema::hasTable('pricing_plans')) {
                $plans = app(PricingPlanService::class)->activePlans();
            }

            $view->with('pricingPlans', $plans);
        });
    }
}
