<?php

namespace App\Modules\Credits\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditWallet extends Model
{
    protected $table = 'credit_wallets';

    protected $fillable = [
        'user_id',
        'balance',
        'reserved_balance',
        'lifetime_earned',
        'lifetime_spent',
        'lifetime_refunded',
        'last_granted_at',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'integer',
            'reserved_balance' => 'integer',
            'lifetime_earned' => 'integer',
            'lifetime_spent' => 'integer',
            'lifetime_refunded' => 'integer',
            'last_granted_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function availableBalance(): int
    {
        return max(0, $this->balance - $this->reserved_balance);
    }
}
