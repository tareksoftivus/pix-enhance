<?php

namespace App\Modules\Credits\Providers;

use App\Modules\Credits\Listeners\GrantCreditsForSuccessfulPayment;
use App\Modules\Credits\Listeners\GrantSignupCredits;
use App\Modules\Credits\Listeners\RevokeCreditsForRefund;
use App\Modules\Credits\Services\CreditCheckoutService;
use App\Modules\Credits\Services\CreditService;
use App\Modules\PaymentGateways\Events\PaymentSucceeded;
use App\Modules\PaymentGateways\Events\RefundProcessed;
use App\Modules\Shared\Support\BasePanelModuleProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

class CreditsServiceProvider extends BasePanelModuleProvider
{
    public function register(): void
    {
        $this->app->singleton(CreditService::class);
        $this->app->singleton(CreditCheckoutService::class);
    }

    protected function bootModule(array $module): void
    {
        Event::listen(Registered::class, GrantSignupCredits::class);
        Event::listen(PaymentSucceeded::class, GrantCreditsForSuccessfulPayment::class);
        Event::listen(RefundProcessed::class, RevokeCreditsForRefund::class);

        View::composer('components.layouts.user', function ($view): void {
            $user = auth()->user();

            $view->with('creditSummary', ($user && Schema::hasTable('credit_wallets'))
                ? app(CreditService::class)->summaryFor($user)
                : null);
        });
    }
}
