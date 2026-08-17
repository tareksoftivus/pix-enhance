<?php

namespace App\Modules\PricingPlan\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PricingPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pricing_plans';

    protected $fillable = [
        'name',
        'slug',
        'tagline',
        'icon',
        'price_monthly',
        'price_yearly',
        'credits_monthly',
        'features',
        'cta_label',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'price_monthly' => 'integer',
            'price_yearly' => 'integer',
            'credits_monthly' => 'integer',
            'features' => 'array',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('price_monthly');
    }
}
