<?php

namespace App\Modules\Billing\Services;

use App\Models\User;
use App\Modules\Billing\Models\BillingInvoice;
use App\Modules\Credits\Models\CreditOrder;
use App\Modules\Credits\Services\CreditCheckoutService;
use App\Modules\Credits\Services\CreditService;
use App\Modules\PaymentGateways\Models\Payment;
use App\Modules\PaymentGateways\Models\Refund;
use App\Modules\PricingPlan\Models\PricingPlan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class BillingService
{
    public function __construct(
        protected CreditService $credits,
        protected CreditCheckoutService $checkout
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function workspaceFor(User $user): array
    {
        $this->syncMissingInvoicesForUser($user);

        $payments = $this->paymentsFor($user, [], 8);
        $invoices = $this->invoicesFor($user, [], 8);
        $creditTransactions = $this->credits->ledgerFor($user, [], 8);
        $orders = $this->ordersFor($user, [], 8);

        return [
            'summary' => $this->summaryFor($user),
            'credit_summary' => $this->credits->summaryFor($user),
            'credit_packs' => $this->checkout->packs(),
            'pricing_plans' => PricingPlan::query()->active()->ordered()->get(),
            'credit_transactions' => $creditTransactions,
            'orders' => $orders,
            'payments' => $payments,
            'invoices' => $invoices,
            'current_plan' => $this->currentPlanFor($user),
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function ordersFor(User $user, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $orders = CreditOrder::query()
            ->with('payment')
            ->forUser($user->getKey())
            ->when(! empty($filters['status']), fn (Builder $query): Builder => $query->where('status', $filters['status']))
            ->latest('created_at')
            ->paginate($perPage)
            ->withQueryString();

        $invoicesByPaymentId = BillingInvoice::query()
            ->whereIn('payment_id', $orders->pluck('payment_id')->filter())
            ->get()
            ->keyBy('payment_id');

        $orders->getCollection()->each(function (CreditOrder $order) use ($invoicesByPaymentId): void {
            $order->setRelation('invoice', $order->payment_id ? $invoicesByPaymentId->get($order->payment_id) : null);
        });

        return $orders;
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paymentsFor(User $user, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Payment::query()
            ->with('refunds')
            ->where('user_type', $user->getMorphClass())
            ->where('user_id', $user->getKey())
            ->when(! empty($filters['status']), fn (Builder $query): Builder => $query->where('status', $filters['status']))
            ->when(! empty($filters['search']), function (Builder $query) use ($filters): void {
                $search = (string) $filters['search'];

                $query->where(function (Builder $inner) use ($search): void {
                    $inner
                        ->where('uuid', 'like', "%{$search}%")
                        ->orWhere('gateway_payment_id', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function invoicesFor(User $user, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return BillingInvoice::query()
            ->with('payment')
            ->forBillable($user)
            ->when(! empty($filters['status']), fn (Builder $query): Builder => $query->where('status', $filters['status']))
            ->when(! empty($filters['search']), function (Builder $query) use ($filters): void {
                $search = (string) $filters['search'];

                $query->where(function (Builder $inner) use ($search): void {
                    $inner
                        ->where('number', 'like', "%{$search}%")
                        ->orWhere('metadata', 'like', "%{$search}%");
                });
            })
            ->latest('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function listInvoices(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $this->syncRecentPayments();

        return BillingInvoice::query()
            ->with(['billable', 'payment'])
            ->when(! empty($filters['status']), fn (Builder $query): Builder => $query->where('status', $filters['status']))
            ->when(! empty($filters['search']), function (Builder $query) use ($filters): void {
                $search = (string) $filters['search'];

                $query->where(function (Builder $inner) use ($search): void {
                    $inner
                        ->where('number', 'like', "%{$search}%")
                        ->orWhere('metadata', 'like', "%{$search}%")
                        ->orWhereHasMorph('billable', [User::class], function (Builder $billable) use ($search): void {
                            $billable
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @return array<string, mixed>
     */
    public function summaryFor(?User $user = null): array
    {
        $invoiceQuery = BillingInvoice::query();
        $paymentQuery = Payment::query();

        if ($user) {
            $invoiceQuery->forBillable($user);
            $paymentQuery
                ->where('user_type', $user->getMorphClass())
                ->where('user_id', $user->getKey());
        }

        return [
            'paid_invoices' => (clone $invoiceQuery)->where('status', BillingInvoice::STATUS_PAID)->count(),
            'open_invoices' => (clone $invoiceQuery)->where('status', BillingInvoice::STATUS_OPEN)->count(),
            'refunded_invoices' => (clone $invoiceQuery)->where('status', BillingInvoice::STATUS_REFUNDED)->count(),
            'gross_revenue' => (float) (clone $invoiceQuery)->whereIn('status', [BillingInvoice::STATUS_PAID, BillingInvoice::STATUS_REFUNDED])->sum('amount_paid'),
            'refunded_revenue' => (float) (clone $invoiceQuery)->sum('amount_refunded'),
            'pending_payments' => (clone $paymentQuery)->where('status', 'pending')->count(),
            'failed_payments' => (clone $paymentQuery)->where('status', 'failed')->count(),
        ];
    }

    public function invoiceForPayment(Payment $payment): BillingInvoice
    {
        if (! Schema::hasTable('billing_invoices')) {
            throw new \RuntimeException('Billing invoices table is not available.');
        }

        $payment->loadMissing('refunds');
        $metadata = $payment->metadata ?? [];
        $status = $this->statusForPayment($payment);
        $paidAmount = $status === BillingInvoice::STATUS_PAID || $status === BillingInvoice::STATUS_REFUNDED
            ? (float) $payment->amount
            : 0.0;
        $refundedAmount = (float) $payment->refunds
            ->where('status', 'completed')
            ->sum(fn (Refund $refund): float => (float) $refund->amount);

        return BillingInvoice::query()->updateOrCreate(
            ['payment_id' => $payment->id],
            [
                'number' => BillingInvoice::query()->where('payment_id', $payment->id)->value('number') ?: $this->nextInvoiceNumber(),
                'billable_type' => $payment->user_type,
                'billable_id' => $payment->user_id,
                'status' => $status,
                'type' => 'invoice',
                'currency' => strtoupper((string) $payment->currency),
                'subtotal' => (float) $payment->amount,
                'tax_total' => 0,
                'discount_total' => 0,
                'total' => (float) $payment->amount,
                'amount_paid' => $paidAmount,
                'amount_refunded' => $refundedAmount,
                'line_items' => [$this->lineItemForPayment($payment)],
                'metadata' => array_merge($metadata, [
                    'payment_uuid' => $payment->uuid,
                    'payment_gateway' => $payment->gateway,
                    'gateway_payment_id' => $payment->gateway_payment_id,
                ]),
                'issued_at' => $payment->created_at ?? now(),
                'due_at' => $payment->status === 'pending'
                    ? ($payment->created_at ?? now())->copy()->addDays((int) config('billing.invoice_due_days', 7))
                    : null,
                'paid_at' => $payment->paid_at,
                'voided_at' => $payment->status === 'failed' ? now() : null,
            ]
        );
    }

    public function syncRefund(Refund $refund): ?BillingInvoice
    {
        $refund->loadMissing('payment.refunds');
        $payment = $refund->payment;

        if (! $payment) {
            return null;
        }

        return $this->invoiceForPayment($payment);
    }

    public function syncMissingInvoicesForUser(User $user, int $limit = 50): void
    {
        if (! Schema::hasTable('billing_invoices')) {
            return;
        }

        Payment::query()
            ->with('refunds')
            ->where('user_type', $user->getMorphClass())
            ->where('user_id', $user->getKey())
            ->whereNotIn('id', BillingInvoice::query()->whereNotNull('payment_id')->select('payment_id'))
            ->latest('created_at')
            ->limit($limit)
            ->get()
            ->each(fn (Payment $payment): BillingInvoice => $this->invoiceForPayment($payment));
    }

    public function syncRecentPayments(int $limit = 100): void
    {
        if (! Schema::hasTable('billing_invoices')) {
            return;
        }

        Payment::query()
            ->with('refunds')
            ->whereNotIn('id', BillingInvoice::query()->whereNotNull('payment_id')->select('payment_id'))
            ->latest('created_at')
            ->limit($limit)
            ->get()
            ->each(fn (Payment $payment): BillingInvoice => $this->invoiceForPayment($payment));
    }

    public function markPaid(BillingInvoice $invoice): BillingInvoice
    {
        $invoice->forceFill([
            'status' => BillingInvoice::STATUS_PAID,
            'amount_paid' => (float) $invoice->total,
            'paid_at' => $invoice->paid_at ?? now(),
            'voided_at' => null,
        ])->save();

        return $invoice->fresh(['billable', 'payment']);
    }

    public function void(BillingInvoice $invoice): BillingInvoice
    {
        $invoice->forceFill([
            'status' => BillingInvoice::STATUS_VOID,
            'voided_at' => now(),
        ])->save();

        return $invoice->fresh(['billable', 'payment']);
    }

    /**
     * @return array<string, mixed>
     */
    public function receiptPayload(BillingInvoice $invoice): array
    {
        $invoice->loadMissing(['billable', 'payment.refunds']);

        return [
            'company' => [
                'name' => config('billing.receipt_company', config('app.name')),
                'email' => config('billing.receipt_email'),
                'address' => config('billing.receipt_address'),
            ],
            'invoice' => $invoice,
            'payment' => $invoice->payment,
            'customer' => $invoice->billable,
            'line_items' => collect($invoice->line_items ?? []),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function currentPlanFor(User $user): ?array
    {
        $transaction = $user->creditTransactions()
            ->where('reason', 'pricing_plan_purchase')
            ->latest('created_at')
            ->first();

        if (! $transaction) {
            return null;
        }

        return [
            'name' => $transaction->metadata['pricing_plan_name'] ?? __('Plan'),
            'slug' => $transaction->metadata['pricing_plan_slug'] ?? null,
            'period' => $transaction->metadata['pricing_period'] ?? null,
            'credits' => (int) $transaction->amount,
            'started_at' => $transaction->created_at,
        ];
    }

    protected function statusForPayment(Payment $payment): string
    {
        return match ($payment->status) {
            'completed' => BillingInvoice::STATUS_PAID,
            'failed' => BillingInvoice::STATUS_FAILED,
            'refunded' => BillingInvoice::STATUS_REFUNDED,
            default => BillingInvoice::STATUS_OPEN,
        };
    }

    /**
     * @return array<string, mixed>
     */
    protected function lineItemForPayment(Payment $payment): array
    {
        $metadata = $payment->metadata ?? [];
        $name = $metadata['credit_pack_name']
            ?? $metadata['pricing_plan_name']
            ?? $payment->description
            ?? __('Billing item');

        return [
            'name' => $name,
            'description' => $payment->description,
            'quantity' => 1,
            'credits' => (int) ($metadata['credits'] ?? 0),
            'unit_amount' => (float) $payment->amount,
            'total' => (float) $payment->amount,
            'currency' => strtoupper((string) $payment->currency),
        ];
    }

    protected function nextInvoiceNumber(): string
    {
        $prefix = Str::upper((string) config('billing.invoice_prefix', 'INV'));

        do {
            $number = $prefix.'-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
        } while (BillingInvoice::query()->where('number', $number)->exists());

        return $number;
    }
}
