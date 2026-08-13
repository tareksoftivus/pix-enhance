<?php

namespace App\Modules\Currencies\Services;

use App\Modules\Currencies\Models\Currency;
use App\Modules\Shared\Traits\HasCrudOperations;

class CurrenciesService
{
    use HasCrudOperations;

    protected string $model = Currency::class;

    /** @var array<string> */
    protected array $searchable = ['code', 'name'];

    /** @var array<string> */
    protected array $filterable = ['is_active'];

    /** @var array<string> */
    protected array $sortable = ['code', 'name', 'exchange_rate', 'sort_order'];

    protected string $defaultSortBy = 'sort_order';

    protected string $defaultSortOrder = 'asc';
}
