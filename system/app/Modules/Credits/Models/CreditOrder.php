<?php

namespace App\Modules\Credits\Models;

use App\Models\User;
use App\Modules\PaymentGateways\Models\Payment;
use App\Modules\PricingPlan\Models\PricingPlan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditOrder extends Model
{
    protected $table = 'credit_orders';

    protected $fillable = [
        'uuid',
        'user_id',
        'type',
        'reference',
        'name',
        'credits',
        'pricing_plan_id',
        'gateway',
        'subtotal',
        'fee',
        'total',
        'currency',
        'status',
        'payment_id',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'credits' => 'integer',
            'subtotal' => 'decimal:2',
            'fee' => 'decimal:2',
            'total' => 'decimal:2',
            'metadata' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function pricingPlan(): BelongsTo
    {
        return $this->belongsTo(PricingPlan::class);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function isPack(): bool
    {
        return $this->type === 'pack';
    }

    public function isPlan(): bool
    {
        return $this->type === 'plan';
    }

    /**
     * @return array{label: string, variant: string, icon: string}
     */
    public function statusMeta(): array
    {
        return match ($this->status) {
            'completed' => ['label' => __('Completed'), 'variant' => 'success', 'icon' => 'check-circle'],
            'pending' => ['label' => __('Pending'), 'variant' => 'warning', 'icon' => 'clock'],
            'failed' => ['label' => __('Failed'), 'variant' => 'danger', 'icon' => 'x-circle'],
            'cancelled' => ['label' => __('Cancelled'), 'variant' => 'neutral', 'icon' => 'ban'],
            default => ['label' => __('Pending'), 'variant' => 'neutral', 'icon' => 'clock'],
        };
    }
}
