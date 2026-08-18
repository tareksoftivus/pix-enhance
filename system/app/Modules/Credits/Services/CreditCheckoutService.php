<?php

namespace App\Modules\Credits\Services;

use App\Models\User;
use App\Modules\PaymentGateways\Models\Payment;
use App\Modules\PaymentGateways\Services\PaymentService;
use App\Modules\PricingPlan\Models\PricingPlan;
use Illuminate\Support\Arr;

class CreditCheckoutService
{
    public function __construct(
        protected CreditService $credits,
        protected PaymentService $payments
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
     * @return array<string, mixed>
     */
    public function purchasePack(User $user, string $slug): array
    {
        $pack = collect($this->packs())->firstWhere('slug', $slug);

        abort_if(! $pack, 404);

        $checkout = $this->payments->charge($pack['price'], $pack['currency'], [
            'description' => __(':credits credit pack', ['credits' => number_format($pack['credits'])]),
            'user_id' => $user->id,
            'user_type' => $user->getMorphClass(),
            'metadata' => [
                'credits_module' => true,
                'credits_reason' => 'credit_pack_purchase',
                'credits' => $pack['credits'],
                'credit_pack_slug' => $pack['slug'],
                'credit_pack_name' => $pack['name'],
            ],
        ]);

        return $this->normalizeCheckoutResult($checkout);
    }

    /**
     * @return array<string, mixed>
     */
    public function purchasePlan(User $user, PricingPlan $plan): array
    {
        abort_unless($plan->is_active, 404);

        if ((int) $plan->price_monthly === 0) {
            $transaction = $this->credits->grant(
                $user,
                (int) $plan->credits_monthly,
                'pricing_plan_purchase',
                $plan,
                $this->planMetadata($plan, 'free'),
                'plan:'.$plan->id.':user:'.$user->id.':free'
            );

            return [
                'payment' => null,
                'response' => null,
                'transaction' => $transaction,
                'status' => 'completed',
                'redirect_url' => null,
            ];
        }

        $checkout = $this->payments->charge((float) $plan->price_monthly, 'USD', [
            'description' => __(':plan monthly plan', ['plan' => $plan->name]),
            'user_id' => $user->id,
            'user_type' => $user->getMorphClass(),
            'metadata' => array_merge($this->planMetadata($plan, 'monthly'), [
                'credits_module' => true,
                'credits_reason' => 'pricing_plan_purchase',
                'credits' => (int) $plan->credits_monthly,
            ]),
        ]);

        return $this->normalizeCheckoutResult($checkout);
    }

    /**
     * @param  array{payment: Payment, response: mixed}  $checkout
     * @return array<string, mixed>
     */
    protected function normalizeCheckoutResult(array $checkout): array
    {
        return [
            'payment' => $checkout['payment'],
            'response' => $checkout['response'],
            'transaction' => null,
            'status' => $checkout['payment']->status,
            'redirect_url' => $checkout['response']->redirectUrl,
            'client_data' => $checkout['response']->clientData,
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
