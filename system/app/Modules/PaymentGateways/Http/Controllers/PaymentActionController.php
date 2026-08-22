<?php

namespace App\Modules\PaymentGateways\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\PaymentGateways\Exceptions\PaymentException;
use App\Modules\PaymentGateways\Models\Payment;
use App\Modules\PaymentGateways\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class PaymentActionController extends Controller
{
    public function __construct(
        protected PaymentService $payments
    ) {}

    public function show(Payment $payment): View|RedirectResponse
    {
        $this->authorizePayment($payment);

        if ($payment->status !== 'pending') {
            return redirect()->route('user.billing');
        }

        $clientData = $payment->metadata['client_data'] ?? null;

        if (! is_array($clientData)) {
            return redirect()
                ->route('user.billing')
                ->with('error', __('This payment method did not return the data required to continue.'));
        }

        return view('payment-gateways::action', compact('payment', 'clientData'));
    }

    public function complete(Payment $payment, Request $request): RedirectResponse
    {
        $this->authorizePayment($payment);

        try {
            $verified = $this->payments->verify($request, $payment->gateway);
        } catch (PaymentException $e) {
            return redirect()
                ->route('user.billing')
                ->with('error', $e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return redirect()
                ->route('user.billing')
                ->with('error', __('We could not verify this payment. Please check your payment history or contact support.'));
        }

        if (! $verified->is($payment)) {
            abort(404);
        }

        if ($verified->status === 'completed') {
            return redirect()
                ->route('user.billing')
                ->with('success', __('Payment completed. Credits were added to your wallet.'));
        }

        if ($verified->status === 'failed') {
            return redirect()
                ->route('user.billing')
                ->with('error', __('Payment failed. Please try again or use another payment method.'));
        }

        return redirect()
            ->route('user.billing')
            ->with('status', __('Payment is still pending. We will update your wallet as soon as it is confirmed.'));
    }

    protected function authorizePayment(Payment $payment): void
    {
        abort_unless(
            $payment->user_id === auth()->id() && $payment->user_type === auth()->user()?->getMorphClass(),
            404
        );
    }
}
