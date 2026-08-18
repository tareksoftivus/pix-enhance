<?php

namespace App\Modules\Credits\Services;

use App\Models\User;
use App\Modules\Credits\Exceptions\InsufficientCreditsException;
use App\Modules\Credits\Models\CreditReservation;
use App\Modules\Credits\Models\CreditTransaction;
use App\Modules\Credits\Models\CreditWallet;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreditService
{
    public function walletFor(User $user): CreditWallet
    {
        return CreditWallet::query()->firstOrCreate(['user_id' => $user->id]);
    }

    /**
     * @return array<string, int>
     */
    public function summaryFor(User $user): array
    {
        $wallet = $this->walletFor($user);

        return [
            'balance' => $wallet->balance,
            'reserved' => $wallet->reserved_balance,
            'available' => $wallet->availableBalance(),
            'lifetime_earned' => $wallet->lifetime_earned,
            'lifetime_spent' => $wallet->lifetime_spent,
            'lifetime_refunded' => $wallet->lifetime_refunded,
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function ledgerFor(User $user, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return CreditTransaction::query()
            ->with('reference')
            ->forUser((int) $user->getKey())
            ->when(! empty($filters['reason']), fn ($query) => $query->where('reason', $filters['reason']))
            ->latest('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function listTransactions(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return CreditTransaction::query()
            ->with(['user', 'reference'])
            ->when(! empty($filters['search']), function ($query) use ($filters): void {
                $search = (string) $filters['search'];

                $query->where(function ($inner) use ($search): void {
                    $inner
                        ->where('reason', 'like', "%{$search}%")
                        ->orWhere('metadata', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search): void {
                            $userQuery
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when(! empty($filters['reason']), fn ($query) => $query->where('reason', $filters['reason']))
            ->latest('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $metadata
     */
    public function grant(User $user, int $amount, string $reason, ?Model $reference = null, array $metadata = [], ?string $idempotencyKey = null): CreditTransaction
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Credit grant amount must be greater than zero.');
        }

        return $this->record($user, $amount, $reason, $reference, $metadata, $idempotencyKey);
    }

    /**
     * @param  array<string, mixed>  $metadata
     */
    public function spend(User $user, int $amount, string $reason, ?Model $reference = null, array $metadata = [], ?string $idempotencyKey = null): CreditTransaction
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Credit spend amount must be greater than zero.');
        }

        return $this->record($user, -$amount, $reason, $reference, $metadata, $idempotencyKey);
    }

    /**
     * @param  array<string, mixed>  $metadata
     */
    public function revoke(User $user, int $amount, string $reason, ?Model $reference = null, array $metadata = [], ?string $idempotencyKey = null): ?CreditTransaction
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Credit revoke amount must be greater than zero.');
        }

        $wallet = $this->walletFor($user);
        $amountToRevoke = min($amount, $wallet->availableBalance());

        if ($amountToRevoke === 0) {
            return null;
        }

        return $this->spend($user, $amountToRevoke, $reason, $reference, array_merge($metadata, [
            'requested_amount' => $amount,
        ]), $idempotencyKey);
    }

    /**
     * @param  array<string, mixed>  $metadata
     */
    public function reserve(User $user, int $amount, ?Model $reservable = null, array $metadata = [], ?string $idempotencyKey = null): CreditReservation
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Credit reservation amount must be greater than zero.');
        }

        if ($idempotencyKey) {
            $existing = CreditReservation::query()->where('idempotency_key', $idempotencyKey)->first();

            if ($existing) {
                return $existing;
            }
        }

        return DB::transaction(function () use ($user, $amount, $reservable, $metadata, $idempotencyKey): CreditReservation {
            $wallet = CreditWallet::query()->where('user_id', $user->id)->lockForUpdate()->first()
                ?? CreditWallet::query()->create(['user_id' => $user->id]);

            if ($wallet->availableBalance() < $amount) {
                throw new InsufficientCreditsException($wallet->availableBalance(), $amount);
            }

            $wallet->increment('reserved_balance', $amount);

            return CreditReservation::query()->create([
                'user_id' => $user->id,
                'amount' => $amount,
                'status' => 'active',
                'reservable_type' => $reservable?->getMorphClass(),
                'reservable_id' => $reservable?->getKey(),
                'idempotency_key' => $idempotencyKey,
                'metadata' => $metadata,
                'expires_at' => now()->addHours(2),
            ]);
        });
    }

    public function capture(CreditReservation $reservation, string $reason = 'render_spend'): CreditTransaction
    {
        if (! $reservation->isActive()) {
            throw new \InvalidArgumentException('Only active credit reservations can be captured.');
        }

        return DB::transaction(function () use ($reservation, $reason): CreditTransaction {
            $wallet = CreditWallet::query()->where('user_id', $reservation->user_id)->lockForUpdate()->firstOrFail();
            $wallet->decrement('reserved_balance', min($wallet->reserved_balance, $reservation->amount));

            $reservation->forceFill([
                'status' => 'captured',
                'captured_at' => now(),
            ])->save();

            return $this->spend(
                $reservation->user,
                $reservation->amount,
                $reason,
                $reservation->reservable,
                $reservation->metadata ?? [],
                'reservation:'.$reservation->id.':capture'
            );
        });
    }

    public function release(CreditReservation $reservation): CreditReservation
    {
        if (! $reservation->isActive()) {
            return $reservation;
        }

        return DB::transaction(function () use ($reservation): CreditReservation {
            $wallet = CreditWallet::query()->where('user_id', $reservation->user_id)->lockForUpdate()->firstOrFail();
            $wallet->decrement('reserved_balance', min($wallet->reserved_balance, $reservation->amount));

            $reservation->forceFill([
                'status' => 'released',
                'released_at' => now(),
            ])->save();

            return $reservation->fresh();
        });
    }

    /**
     * @param  array<string, mixed>  $metadata
     */
    protected function record(User $user, int $signedAmount, string $reason, ?Model $reference = null, array $metadata = [], ?string $idempotencyKey = null): CreditTransaction
    {
        if ($idempotencyKey) {
            $existing = CreditTransaction::query()->where('idempotency_key', $idempotencyKey)->first();

            if ($existing) {
                return $existing;
            }
        }

        return DB::transaction(function () use ($user, $signedAmount, $reason, $reference, $metadata, $idempotencyKey): CreditTransaction {
            $wallet = CreditWallet::query()->where('user_id', $user->id)->lockForUpdate()->first()
                ?? CreditWallet::query()->create(['user_id' => $user->id]);

            if ($signedAmount < 0 && $wallet->availableBalance() < abs($signedAmount)) {
                throw new InsufficientCreditsException($wallet->availableBalance(), abs($signedAmount));
            }

            $newBalance = $wallet->balance + $signedAmount;
            $updates = ['balance' => $newBalance];

            if ($signedAmount > 0) {
                $updates['lifetime_earned'] = $wallet->lifetime_earned + $signedAmount;
                $updates['last_granted_at'] = now();
            } else {
                $updates['lifetime_spent'] = $wallet->lifetime_spent + abs($signedAmount);
            }

            $wallet->forceFill($updates)->save();

            return CreditTransaction::query()->create([
                'user_id' => $user->id,
                'amount' => $signedAmount,
                'balance_after' => $newBalance,
                'reason' => $reason,
                'reference_type' => $reference?->getMorphClass(),
                'reference_id' => $reference?->getKey(),
                'idempotency_key' => $idempotencyKey,
                'metadata' => $metadata,
            ]);
        });
    }
}
