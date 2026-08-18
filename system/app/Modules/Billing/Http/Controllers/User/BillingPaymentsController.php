<?php

namespace App\Modules\Billing\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Modules\Billing\Models\BillingInvoice;
use App\Modules\Billing\Services\BillingService;
use App\Modules\PaymentGateways\Models\Payment;
use Illuminate\Http\Response;
use Illuminate\View\View;

class BillingPaymentsController extends Controller
{
    public function __construct(
        protected BillingService $billing
    ) {}

    public function show(Payment $payment): View
    {
        $this->authorizePayment($payment);

        $invoice = $this->billing->invoiceForPayment($payment);

        return view('billing::user.payment', compact('payment', 'invoice'));
    }

    public function invoice(BillingInvoice $billingInvoice): View
    {
        $this->authorizeInvoice($billingInvoice);

        $payload = $this->billing->receiptPayload($billingInvoice);

        return view('billing::user.invoice', $payload);
    }

    public function download(BillingInvoice $billingInvoice): Response
    {
        $this->authorizeInvoice($billingInvoice);

        $payload = $this->billing->receiptPayload($billingInvoice);
        $invoice = $payload['invoice'];
        $payment = $payload['payment'];
        $customer = $payload['customer'];

        $lines = [
            (string) $payload['company']['name'],
            'Invoice '.$invoice->number,
            'Status: '.ucfirst((string) $invoice->status),
            'Issued: '.($invoice->issued_at?->format('Y-m-d H:i') ?? ''),
            '',
            'Bill to: '.($customer?->name ?? $customer?->email ?? 'Customer'),
            'Email: '.($customer?->email ?? ''),
            '',
            'Items:',
        ];

        foreach ($payload['line_items'] as $item) {
            $lines[] = '- '.$item['name'].' | '.strtoupper($invoice->currency).' '.number_format((float) $item['total'], 2);
        }

        $lines = array_merge($lines, [
            '',
            'Subtotal: '.strtoupper($invoice->currency).' '.number_format((float) $invoice->subtotal, 2),
            'Total: '.strtoupper($invoice->currency).' '.number_format((float) $invoice->total, 2),
            'Paid: '.strtoupper($invoice->currency).' '.number_format((float) $invoice->amount_paid, 2),
            'Refunded: '.strtoupper($invoice->currency).' '.number_format((float) $invoice->amount_refunded, 2),
            '',
            'Payment: '.($payment?->uuid ?? 'N/A'),
        ]);

        return response(implode("\n", $lines), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$invoice->number.'.txt"',
        ]);
    }

    protected function authorizePayment(Payment $payment): void
    {
        abort_unless(
            $payment->user_type === auth()->user()->getMorphClass()
            && (int) $payment->user_id === (int) auth()->id(),
            403
        );
    }

    protected function authorizeInvoice(BillingInvoice $invoice): void
    {
        abort_unless(
            $invoice->billable_type === auth()->user()->getMorphClass()
            && (int) $invoice->billable_id === (int) auth()->id(),
            403
        );
    }
}
