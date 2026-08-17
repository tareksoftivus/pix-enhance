<?php

namespace App\Modules\PricingPlan\Database\Seeders;

use App\Modules\PricingPlan\Models\PricingPlan;
use Illuminate\Database\Seeder;

class PricingPlanSeeder extends Seeder
{
    public function run(): void
    {
        PricingPlan::query()->updateOrCreate(
            ['slug' => 'starter'],
            [
                'name' => 'Starter',
                'tagline' => 'For side projects and occasional image rescue jobs.',
                'icon' => 'ph-feather',
                'price_monthly' => 19,
                'price_yearly' => 180,
                'credits_monthly' => 300,
                'features' => [
                    '300 image enhancement credits per month',
                    'Upscale up to 4x with 4K output',
                    'Face restoration and denoise',
                    'Batch processing for 10 images',
                ],
                'cta_label' => 'Start free trial',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 1,
            ]
        );

        PricingPlan::query()->updateOrCreate(
            ['slug' => 'pro'],
            [
                'name' => 'Pro',
                'tagline' => 'For photographers, agencies and busy storefronts.',
                'icon' => 'ph-sparkle',
                'price_monthly' => 49,
                'price_yearly' => 468,
                'credits_monthly' => 1500,
                'features' => [
                    'Everything in Starter',
                    '1,500 image enhancement credits per month',
                    'Upscale up to 16x with 16K output',
                    'All nine AI models including background removal',
                    'Batch processing for 200 images',
                    'API access and webhooks',
                    'Commercial licence included',
                ],
                'cta_label' => 'Get started',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2,
            ]
        );

        PricingPlan::query()->updateOrCreate(
            ['slug' => 'scale'],
            [
                'name' => 'Scale',
                'tagline' => 'For marketplaces and product teams running image pipelines.',
                'icon' => 'ph-kanban',
                'price_monthly' => 199,
                'price_yearly' => 1908,
                'credits_monthly' => 10000,
                'features' => [
                    'Everything in Pro',
                    '10,000 image enhancement credits per month',
                    'Dedicated GPU capacity',
                    'Custom storage with S3, R2 or GCS',
                    'SSO, audit logs and team roles',
                    'Priority support and implementation help',
                ],
                'cta_label' => 'Talk to sales',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 3,
            ]
        );
    }
}
