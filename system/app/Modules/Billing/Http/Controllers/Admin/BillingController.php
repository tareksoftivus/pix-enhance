<?php

namespace App\Modules\Billing\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Billing\Models\BillingInvoice;
use App\Modules\Billing\Services\BillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class BillingController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:billing.view', only: ['index', 'show']),
            new Middleware('permission:billing.manage', only: ['markPaid', 'void']),
        ];
    }

    public function __construct(
        protected BillingService $billing
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'status']);
        $invoices = $this->billing->listInvoices($filters, $request->integer('per_page') ?: 15);
        $summary = $this->billing->summaryFor();

        return view('billing::admin.index', compact('invoices', 'summary', 'filters'));
    }

    public function show(BillingInvoice $billingInvoice): View
    {
        $payload = $this->billing->receiptPayload($billingInvoice);

        return view('billing::admin.show', $payload);
    }

    public function markPaid(BillingInvoice $billingInvoice): RedirectResponse
    {
        if ($billingInvoice->status === BillingInvoice::STATUS_VOID) {
            return back()->with('error', __('Void invoices cannot be marked as paid.'));
        }

        $this->billing->markPaid($billingInvoice);

        return back()->with('success', __('Invoice marked as paid.'));
    }

    public function void(BillingInvoice $billingInvoice): RedirectResponse
    {
        if ($billingInvoice->isPaid()) {
            return back()->with('error', __('Paid invoices cannot be voided.'));
        }

        $this->billing->void($billingInvoice);

        return back()->with('success', __('Invoice voided.'));
    }
}
