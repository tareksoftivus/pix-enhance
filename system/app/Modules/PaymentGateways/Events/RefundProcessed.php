<?php

namespace App\Modules\PaymentGateways\Events;

use App\Modules\PaymentGateways\Models\Refund;

class RefundProcessed
{
    public function __construct(
        public Refund $refund
    ) {}
}
