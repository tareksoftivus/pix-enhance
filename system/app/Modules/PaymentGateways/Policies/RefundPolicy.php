<?php

namespace App\Modules\PaymentGateways\Policies;

use App\Modules\PaymentGateways\Models\Refund;
use Illuminate\Contracts\Auth\Authenticatable;

class RefundPolicy
{
    public function viewAny(Authenticatable $user): bool
    {
        return $user->can('refunds.view');
    }

    public function view(Authenticatable $user, Refund $refund): bool
    {
        return $user->can('refunds.view');
    }
}
