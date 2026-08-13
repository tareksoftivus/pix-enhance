<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\PaymentGateways\Models\Payment;
use App\Modules\PaymentGateways\Models\Refund;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

/**
 * Populates the payments/refunds tables with a believable transaction history.
 *
 * Idempotent: each payment is keyed on a deterministic uuid derived from its
 * index, so re-running updates rather than duplicates. Refunds are attached to
 * a subset of completed payments.
 */
class DemoPaymentsSeeder extends Seeder
{
    /**
     * Namespace used to derive stable uuids from a demo index.
     */
    protected const DEMO_UUID_NAMESPACE = '6f9619ff-8b86-d011-b42d-00c04fc964ff';

    public function run(): void
    {
        $users = User::query()->orderBy('id')->pluck('id')->all();

        if ($users === []) {
            return;
        }

        $gateways = ['stripe', 'paypal', 'razorpay', 'manual'];
        $methods = ['card', 'card', 'card', 'wallet', 'bank_transfer', 'paypal'];
        $currencies = ['USD', 'USD', 'USD', 'EUR', 'GBP', 'BDT'];
        $descriptions = [
            'Pro plan — monthly subscription',
            'Business plan — annual subscription',
            'Starter plan — monthly subscription',
            'Wallet top-up',
            'Add-on: extra seats',
            'Marketplace listing fee',
            'Priority support upgrade',
        ];

        $count = 42;

        for ($i = 0; $i < $count; $i++) {
            // ~78% completed, remainder pending, plus a couple failed for variety.
            $status = match (true) {
                $i % 13 === 5 => 'failed',
                $i % 4 === 3 => 'pending',
                default => 'completed',
            };

            $userId = $users[$i % count($users)];
            $gateway = $gateways[$i % count($gateways)];
            $amount = $this->amountFor($i);
            $createdAt = now()->subDays(($count - $i))->subHours($i % 24);

            $payment = Payment::firstOrCreate(
                ['uuid' => $this->demoUuid($i)],
                [
                    'user_type' => User::class,
                    'user_id' => $userId,
                    'gateway' => $gateway,
                    'gateway_payment_id' => strtoupper($gateway[0]).'-'.str_pad((string) ($i + 1000), 8, '0', STR_PAD_LEFT),
                    'amount' => $amount,
                    'currency' => $currencies[$i % count($currencies)],
                    'status' => $status,
                    'payment_method' => $methods[$i % count($methods)],
                    'description' => $descriptions[$i % count($descriptions)],
                    'metadata' => ['source' => 'demo-seeder', 'seat_count' => 1 + ($i % 5)],
                    'paid_at' => $status === 'completed' ? $createdAt->copy()->addMinutes(2) : null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]
            );

            // Refund roughly every 9th completed payment (full or partial).
            if ($status === 'completed' && $i % 9 === 4) {
                $partial = $i % 2 === 0;
                $refundAmount = $partial ? round($amount / 2, 2) : $amount;

                Refund::firstOrCreate(
                    ['gateway_refund_id' => 'RF-'.str_pad((string) ($i + 5000), 8, '0', STR_PAD_LEFT)],
                    [
                        'payment_id' => $payment->id,
                        'amount' => $refundAmount,
                        'status' => 'completed',
                        'reason' => $partial ? 'Partial refund — unused seats' : 'Customer requested cancellation',
                        'metadata' => ['source' => 'demo-seeder'],
                        'created_at' => $createdAt->copy()->addDays(2),
                        'updated_at' => $createdAt->copy()->addDays(2),
                    ]
                );
            }
        }
    }

    /**
     * A spread of realistic amounts driven by the index so runs stay stable.
     */
    protected function amountFor(int $i): float
    {
        $tiers = [9.00, 19.00, 29.00, 49.00, 99.00, 149.00, 199.00, 299.00, 15.50, 75.25];

        return $tiers[$i % count($tiers)];
    }

    /**
     * Deterministic uuid v5 from the demo index — stable across re-runs.
     */
    protected function demoUuid(int $i): string
    {
        return Uuid::uuid5(self::DEMO_UUID_NAMESPACE, 'demo-payment-'.$i)->toString();
    }
}
