<?php

namespace App\Modules\PaymentGateways\Services;

use App\Modules\PaymentGateways\Models\Refund;
use App\Modules\Shared\Traits\HasCrudOperations;
use Illuminate\Database\Eloquent\Builder;

class RefundService
{
    use HasCrudOperations;

    protected string $model = Refund::class;

    /** @var array<string> */
    protected array $searchable = ['gateway_refund_id', 'reason'];

    /** @var array<string> */
    protected array $filterable = ['status'];

    protected string $defaultSortBy = 'created_at';

    protected string $defaultSortOrder = 'desc';

    protected function applyEagerLoads(Builder $query): Builder
    {
        return $query->with('payment');
    }
}
