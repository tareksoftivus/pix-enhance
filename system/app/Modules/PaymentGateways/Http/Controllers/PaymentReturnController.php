<?php

namespace App\Modules\PaymentGateways\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\PaymentGateways\Exceptions\PaymentException;
use App\Modules\PaymentGateways\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class PaymentReturnController extends Controller
{
    public function __construct(
        protected PaymentService $payments
    ) {}

    public function return(string $gateway, Request $request): RedirectResponse
    {
        try {
            $payment = $this->payments->verify($request, $gateway);
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

        if ($payment->status === 'completed') {
            return redirect()
                ->route('user.billing')
                ->with('success', __('Payment completed. Credits were added to your wallet.'));
        }

        if ($payment->status === 'failed') {
            return redirect()
                ->route('user.billing')
                ->with('error', __('Payment failed. Please try again or use another payment method.'));
        }

        return redirect()
            ->route('user.billing')
            ->with('status', __('Payment is still pending. We will update your wallet as soon as it is confirmed.'));
    }

    public function cancel(string $gateway): RedirectResponse
    {
        return redirect()
            ->route('user.billing')
            ->with('status', __('Payment was cancelled. No credits were added.'));
    }
}
