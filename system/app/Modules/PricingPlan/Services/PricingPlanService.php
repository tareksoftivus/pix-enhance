<?php

namespace App\Modules\PricingPlan\Services;

use App\Modules\PricingPlan\Models\PricingPlan;
use App\Modules\Shared\Traits\HasCrudOperations;
use Illuminate\Database\Eloquent\Collection;

class PricingPlanService
{
    use HasCrudOperations;

    protected string $model = PricingPlan::class;

    /** @var array<string> */
    protected array $searchable = ['name', 'slug', 'tagline'];

    /** @var array<string> */
    protected array $filterable = ['is_active', 'is_featured'];

    /** @var array<string> */
    protected array $sortable = ['name', 'price_monthly', 'price_yearly', 'sort_order', 'created_at'];

    protected string $defaultSortBy = 'sort_order';

    protected string $defaultSortOrder = 'asc';

    /**
     * @return Collection<int, PricingPlan>
     */
    public function activePlans(): Collection
    {
        return PricingPlan::active()->ordered()->get();
    }

    public function featuredPlan(): ?PricingPlan
    {
        return PricingPlan::active()->featured()->ordered()->first();
    }
}
