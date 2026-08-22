<?php

namespace App\Modules\PaymentGateways\Drivers\Concerns;

trait BuildsGatewayReturnUrls
{
    protected function returnUrlWithReference(?string $url, string $reference): ?string
    {
        if (empty($url) || str_contains($url, 'gateway_payment_id=')) {
            return $url;
        }

        $separator = str_contains($url, '?') ? '&' : '?';

        return $url.$separator.'gateway_payment_id='.rawurlencode($reference);
    }
}
