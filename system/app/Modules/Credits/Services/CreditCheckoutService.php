<?php

namespace App\Modules\Credits\Services;

use App\Models\User;
use App\Modules\Credits\Models\CreditOrder;
use App\Modules\PaymentGateways\Models\Payment;
use App\Modules\PaymentGateways\Services\PaymentGatewayManager;
use App\Modules\PaymentGateways\Services\PaymentService;
use App\Modules\PaymentGatewaySettings\Services\PaymentGatewaySettingsService;
use App\Modules\PricingPlan\Models\PricingPlan;
use App\Modules\SystemNotifications\Services\UserSystemNotificationService;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CreditCheckoutService
{
    public function __construct(
        protected CreditService $credits,
        protected PaymentService $payments,
        protected PaymentGatewayManager $gateways,
        protected PaymentGatewaySettingsService $gatewaySettings,
        protected UserSystemNotificationService $systemNotifications
    ) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function packs(): array
    {
        return collect(config('credits.packs', []))
            ->map(function (array $pack, string $slug): array {
                $credits = (int) Arr::get($pack, 'credits', 0);
                $price = (float) Arr::get($pack, 'price', 0);

                return [
                    'slug' => $slug,
                    'name' => Arr::get($pack, 'name', ucfirst($slug)),
                    'credits' => $credits,
                    'price' => $price,
                    'currency' => strtoupper((string) Arr::get($pack, 'currency', 'USD')),
                    'rate' => $credits > 0 ? $price / $credits : 0,
                    'badge' => Arr::get($pack, 'badge'),
                ];
            })
            ->filter(fn (array $pack): bool => $pack['credits'] > 0 && $pack['price'] > 0)
            ->values()
            ->all();
    }

    /**
     * Gateway names the user may legally select at checkout: the admin-enabled
     * list, or — with none enabled and outside production — the dev-only log
     * gateway (mirrors PaymentGatewayManager::driver(null)'s own fallback) so
     * non-production environments aren't locked out of checkout entirely.
     *
     * @return array<int, string>
     */
    public function selectableGatewayNames(): array
    {
        $names = $this->gateways->getEnabledGatewayNames();

        if (empty($names) && ! app()->environment('production')) {
            $names = ['log'];
        }

        return $names;
    }

    /**
     * Admin-enabled gateways available for the user to pick at checkout, with
     * display metadata and the fee they'd add for the given base amount.
     *
     * @return array<int, array{name: string, label: string, icon: string|null, fee: float, total: float}>
     */
    public function availableGateways(float $amount): array
    {
        return collect($this->selectableGatewayNames())
            ->map(function (string $name) use ($amount): array {
                $fee = $this->gatewaySettings->feeFor($name, $amount);

                return [
                    'name' => $name,
                    'label' => $name === 'log' ? __('Test / development mode') : $this->gatewaySettings->labelFor($name),
                    'icon' => $this->gatewaySettings->iconFor($name),
                    'fee' => $fee['fee'],
                    'total' => $fee['total'],
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Resolve a checkout order preview for a credit pack or pricing plan, for
     * display on the checkout page before the user actually pays — including
     * the fee for the given (or default) gateway.
     *
     * @return array<string, mixed>|null
     */
    public function orderFor(string $type, ?string $identifier, ?string $gateway = null): ?array
    {
        if ($type === 'plan') {
            $plan = $identifier !== null
                ? PricingPlan::query()->active()->find($identifier)
                : null;

            if (! $plan) {
                return null;
            }

            $order = [
                'type' => 'plan',
                'slug' => $plan->slug,
                'id' => $plan->id,
                'name' => $plan->name,
                'credits' => (int) $plan->credits_monthly,
                'price' => (float) $plan->price_monthly,
                'currency' => 'USD',
                'badge' => $plan->is_featured ? __('Popular') : null,
                'plan' => $plan,
            ];
        } else {
            $pack = $identifier !== null
                ? collect($this->packs())->firstWhere('slug', $identifier)
                : null;

            if (! $pack) {
                return null;
            }

            $order = [
                'type' => 'pack',
                'slug' => $pack['slug'],
                'id' => $pack['slug'],
                'name' => $pack['name'],
                'credits' => $pack['credits'],
                'price' => $pack['price'],
                'currency' => $pack['currency'],
                'badge' => $pack['badge'],
                'plan' => null,
            ];
        }

        $gateways = $this->availableGateways($order['price']);
        $selected = $gateway !== null ? collect($gateways)->firstWhere('name', $gateway) : null;
        $selected ??= $gateways[0] ?? null;

        $order['gateways'] = $gateways;
        $order['gateway'] = $selected['name'] ?? null;
        $order['fee'] = $order['price'] > 0 ? (float) ($selected['fee'] ?? 0) : 0.0;
        $order['total'] = $order['price'] > 0 ? (float) ($selected['total'] ?? $order['price']) : 0.0;

        return $order;
    }

    /**
     * @return array<string, mixed>
     */
    public function purchasePack(User $user, string $slug, ?string $gateway = null): array
    {
        $pack = collect($this->packs())->firstWhere('slug', $slug);

        abort_if(! $pack, 404);

        $gatewayName = $gateway ?? $this->gateways->driver()->name();
        $fee = $this->gatewaySettings->feeFor($gatewayName, $pack['price']);

        $order = CreditOrder::create([
            'uuid' => (string) Str::ulid(),
            'user_id' => $user->id,
            'type' => 'pack',
            'reference' => $pack['slug'],
            'name' => $pack['name'],
            'credits' => $pack['credits'],
            'gateway' => $gatewayName,
            'subtotal' => $pack['price'],
            'fee' => $fee['fee'],
            'total' => $fee['total'],
            'currency' => $pack['currency'],
            'status' => 'pending',
        ]);

        try {
            $checkout = $this->payments->charge($fee['total'], $pack['currency'], [
                'gateway' => $gatewayName,
                'description' => __(':credits credit pack', ['credits' => number_format($pack['credits'])]),
                'return_url' => route('payments.return', ['gateway' => $gatewayName]),
                'cancel_url' => route('payments.cancel', ['gateway' => $gatewayName]),
                'user_id' => $user->id,
                'user_type' => $user->getMorphClass(),
                'metadata' => [
                    'credits_module' => true,
                    'credits_reason' => 'credit_pack_purchase',
                    'credits' => $pack['credits'],
                    'credit_pack_slug' => $pack['slug'],
                    'credit_pack_name' => $pack['name'],
                    'credit_order_id' => $order->id,
                    'email' => $user->email,
                    'customer_email' => $user->email,
                    'customer_name' => $user->name,
                ],
            ]);
        } catch (\Throwable $e) {
            $order->update(['status' => 'failed']);

            throw $e;
        }

        $order->update([
            'payment_id' => $checkout['payment']->id,
            'status' => $checkout['payment']->status,
        ]);

        return $this->normalizeCheckoutResult($checkout, $order);
    }

    /**
     * @return array<string, mixed>
     */
    public function purchasePlan(User $user, PricingPlan $plan, ?string $gateway = null): array
    {
        abort_unless($plan->is_active, 404);

        if ((int) $plan->price_monthly === 0) {
            $order = CreditOrder::create([
                'uuid' => (string) Str::ulid(),
                'user_id' => $user->id,
                'type' => 'plan',
                'reference' => (string) $plan->id,
                'name' => $plan->name,
                'credits' => (int) $plan->credits_monthly,
                'pricing_plan_id' => $plan->id,
                'gateway' => null,
                'subtotal' => 0,
                'fee' => 0,
                'total' => 0,
                'currency' => 'USD',
                'status' => 'completed',
            ]);

            $transaction = $this->credits->grant(
                $user,
                (int) $plan->credits_monthly,
                'pricing_plan_purchase',
                $plan,
                $this->planMetadata($plan, 'free'),
                'plan:'.$plan->id.':user:'.$user->id.':free'
            );
            $this->systemNotifications->creditsGranted($user, $transaction);

            return [
                'payment' => null,
                'response' => null,
                'transaction' => $transaction,
                'status' => 'completed',
                'redirect_url' => null,
                'client_data' => null,
                'order' => $order,
            ];
        }

        $gatewayName = $gateway ?? $this->gateways->driver()->name();
        $fee = $this->gatewaySettings->feeFor($gatewayName, (float) $plan->price_monthly);

        $order = CreditOrder::create([
            'uuid' => (string) Str::ulid(),
            'user_id' => $user->id,
            'type' => 'plan',
            'reference' => (string) $plan->id,
            'name' => $plan->name,
            'credits' => (int) $plan->credits_monthly,
            'pricing_plan_id' => $plan->id,
            'gateway' => $gatewayName,
            'subtotal' => (float) $plan->price_monthly,
            'fee' => $fee['fee'],
            'total' => $fee['total'],
            'currency' => 'USD',
            'status' => 'pending',
        ]);

        try {
            $checkout = $this->payments->charge($fee['total'], 'USD', [
                'gateway' => $gatewayName,
                'description' => __(':plan monthly plan', ['plan' => $plan->name]),
                'return_url' => route('payments.return', ['gateway' => $gatewayName]),
                'cancel_url' => route('payments.cancel', ['gateway' => $gatewayName]),
                'user_id' => $user->id,
                'user_type' => $user->getMorphClass(),
                'metadata' => array_merge($this->planMetadata($plan, 'monthly'), [
                    'credits_module' => true,
                    'credits_reason' => 'pricing_plan_purchase',
                    'credits' => (int) $plan->credits_monthly,
                    'credit_order_id' => $order->id,
                    'email' => $user->email,
                    'customer_email' => $user->email,
                    'customer_name' => $user->name,
                ]),
            ]);
        } catch (\Throwable $e) {
            $order->update(['status' => 'failed']);

            throw $e;
        }

        $order->update([
            'payment_id' => $checkout['payment']->id,
            'status' => $checkout['payment']->status,
        ]);

        return $this->normalizeCheckoutResult($checkout, $order);
    }

    /**
     * @param  array{payment: Payment, response: mixed}  $checkout
     * @return array<string, mixed>
     */
    protected function normalizeCheckoutResult(array $checkout, CreditOrder $order): array
    {
        return [
            'payment' => $checkout['payment'],
            'response' => $checkout['response'],
            'transaction' => null,
            'status' => $checkout['payment']->status,
            'redirect_url' => $checkout['response']->redirectUrl,
            'client_data' => $checkout['response']->clientData,
            'message' => $checkout['response']->message,
            'order' => $order,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function planMetadata(PricingPlan $plan, string $period): array
    {
        return [
            'pricing_plan_id' => $plan->id,
            'pricing_plan_name' => $plan->name,
            'pricing_plan_slug' => $plan->slug,
            'pricing_period' => $period,
        ];
    }
}
