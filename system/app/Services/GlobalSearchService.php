<?php

namespace App\Services;

use App\Models\Admin;
use App\Modules\Blogs\Models\Blog;
use App\Modules\NotificationTemplates\Models\NotificationTemplate;
use App\Modules\PaymentGateways\Models\Payment;
use App\Modules\Products\Models\Product;
use Illuminate\Support\Facades\Route;

class GlobalSearchService
{
    /**
     * Searchable modules per guard.
     *
     * Each entry: model, label, icon, columns (LIKE search), route (named), subtitle (optional column).
     *
     * @var array<string, list<array{model: class-string, label: string, icon: string, columns: list<string>, route: string, subtitle?: string}>>
     */
    protected array $searchableModules = [
        'admin' => [
            [
                'model' => Admin::class,
                'label' => 'Users',
                'icon' => 'ph-users',
                'columns' => ['name', 'email'],
                'route' => 'admin.users.show',
                'subtitle' => 'email',
            ],
            [
                'model' => Product::class,
                'label' => 'Products',
                'icon' => 'ph-package',
                'columns' => ['name'],
                'route' => 'admin.products.show',
            ],
            [
                'model' => Blog::class,
                'label' => 'Blogs',
                'icon' => 'ph-article',
                'columns' => ['name'],
                'route' => 'admin.blogs.show',
            ],
            [
                'model' => NotificationTemplate::class,
                'label' => 'Notification Templates',
                'icon' => 'ph-bell',
                'columns' => ['name', 'slug'],
                'route' => 'admin.notification-templates.edit',
            ],
            [
                'model' => Payment::class,
                'label' => 'Payments',
                'icon' => 'ph-credit-card',
                'columns' => ['uuid', 'gateway_payment_id', 'description'],
                'route' => 'admin.payments.show',
            ],
        ],
    ];

    /**
     * Search across multiple models for the given guard.
     *
     * @return list<array{module: string, icon: string, results: list<array{id: int, title: string, subtitle: string|null, url: string}>}>
     */
    public function search(string $query, string $guard, int $limit = 5): array
    {
        $modules = $this->searchableModules[$guard] ?? [];
        $grouped = [];

        foreach ($modules as $module) {
            if (! Route::has($module['route'])) {
                continue;
            }

            $results = $this->searchModel($query, $module, $limit);

            if ($results !== []) {
                $grouped[] = [
                    'module' => $module['label'],
                    'icon' => $module['icon'],
                    'results' => $results,
                ];
            }
        }

        return $grouped;
    }

    /**
     * Search a single model's columns.
     *
     * @return list<array{id: int, title: string, subtitle: string|null, url: string}>
     */
    protected function searchModel(string $query, array $module, int $limit): array
    {
        $modelClass = $module['model'];
        $builder = $modelClass::query();

        $builder->where(function ($q) use ($query, $module) {
            foreach ($module['columns'] as $column) {
                $q->orWhere($column, 'like', "%{$query}%");
            }
        });

        $records = $builder->limit($limit)->get();

        return $records->map(function ($record) use ($module) {
            $titleColumn = $module['columns'][0];
            $subtitleColumn = $module['subtitle'] ?? null;

            return [
                'id' => $record->getKey(),
                'title' => $record->{$titleColumn},
                'subtitle' => $subtitleColumn ? $record->{$subtitleColumn} : null,
                'url' => route($module['route'], $record->getKey()),
            ];
        })->all();
    }
}
