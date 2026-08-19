<?php

namespace App\Modules\Credits\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Modules\Credits\Http\Requests\PurchaseCreditPackRequest;
use App\Modules\Credits\Services\CreditCheckoutService;
use App\Modules\PricingPlan\Models\PricingPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CreditCheckoutController extends Controller
{
    public function __construct(
        protected CreditCheckoutService $checkout
    ) {}

    public function purchasePack(PurchaseCreditPackRequest $request): RedirectResponse
    {
        $result = $this->checkout->purchasePack(
            $request->user(),
            $request->validated('pack'),
            $request->validated('gateway')
        );

        return $this->redirectForCheckout($result, __('Credit purchase started. Complete the payment to add credits.'));
    }

    public function purchasePlan(Request $request, PricingPlan $pricingPlan): RedirectResponse
    {
        $request->validate([
            'gateway' => ['nullable', 'string', Rule::in($this->checkout->selectableGatewayNames())],
        ]);

        $result = $this->checkout->purchasePlan(auth()->user(), $pricingPlan, $request->input('gateway'));

        return $this->redirectForCheckout($result, __('Plan checkout started. Credits are added after payment confirmation.'));
    }

    /**
     * @param  array<string, mixed>  $result
     */
    protected function redirectForCheckout(array $result, string $pendingMessage): RedirectResponse
    {
        if (! empty($result['redirect_url'])) {
            return redirect()->away($result['redirect_url']);
        }

        if (($result['status'] ?? null) === 'completed') {
            $order = $result['order'] ?? null;

            if ($order?->payment_id) {
                return redirect()->route('user.billing')->with('success', __('Credits added to your wallet. Your invoice is ready in Billing.'));
            }

            return back()->with('success', __('Credits added to your wallet.'));
        }

        return back()->with('status', $pendingMessage);
    }
}
