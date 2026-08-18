<?php

namespace App\Modules\Billing\Models;

use App\Modules\PaymentGateways\Models\Payment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BillingInvoice extends Model
{
    use SoftDeletes;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_OPEN = 'open';

    public const STATUS_PAID = 'paid';

    public const STATUS_FAILED = 'failed';

    public const STATUS_REFUNDED = 'refunded';

    public const STATUS_VOID = 'void';

    protected $table = 'billing_invoices';

    protected $fillable = [
        'uuid',
        'number',
        'billable_type',
        'billable_id',
        'payment_id',
        'status',
        'type',
        'currency',
        'subtotal',
        'tax_total',
        'discount_total',
        'total',
        'amount_paid',
        'amount_refunded',
        'line_items',
        'metadata',
        'issued_at',
        'due_at',
        'paid_at',
        'voided_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax_total' => 'decimal:2',
            'discount_total' => 'decimal:2',
            'total' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'amount_refunded' => 'decimal:2',
            'line_items' => 'array',
            'metadata' => 'array',
            'issued_at' => 'datetime',
            'due_at' => 'datetime',
            'paid_at' => 'datetime',
            'voided_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted(): void
    {
        static::creating(function (BillingInvoice $invoice): void {
            $invoice->uuid ??= (string) Str::ulid();
        });
    }

    public function billable(): MorphTo
    {
        return $this->morphTo();
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function scopeForBillable(Builder $query, Model $billable): Builder
    {
        return $query
            ->where('billable_type', $billable->getMorphClass())
            ->where('billable_id', $billable->getKey());
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function formattedTotal(): string
    {
        return strtoupper($this->currency).' '.number_format((float) $this->total, 2);
    }

    /**
     * @return array{label: string, variant: string, icon: string}
     */
    public function statusMeta(): array
    {
        return match ($this->status) {
            self::STATUS_PAID => ['label' => __('Paid'), 'variant' => 'success', 'icon' => 'check-circle'],
            self::STATUS_OPEN => ['label' => __('Open'), 'variant' => 'warning', 'icon' => 'clock'],
            self::STATUS_FAILED => ['label' => __('Failed'), 'variant' => 'danger', 'icon' => 'x-circle'],
            self::STATUS_REFUNDED => ['label' => __('Refunded'), 'variant' => 'neutral', 'icon' => 'undo-2'],
            self::STATUS_VOID => ['label' => __('Void'), 'variant' => 'neutral', 'icon' => 'ban'],
            default => ['label' => __('Draft'), 'variant' => 'neutral', 'icon' => 'file-text'],
        };
    }
}
