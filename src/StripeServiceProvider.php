<?php

namespace JawadAhmed\LaravelStripe;

use Illuminate\Support\ServiceProvider;

final class StripeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/stripe.php', 'stripe');
        $this->app->singleton(StripeGateway::class, fn () => new StripeGateway(
            (string) config('stripe.secret'),
            config('stripe.api_version'),
        ));
    }

    public function boot(): void
    {
        $this->publishes([__DIR__.'/../config/stripe.php' => config_path('stripe.php')], 'stripe-config');
    }
}
