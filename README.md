# Laravel Stripe Payments

Laravel 13 examples for Stripe Payment Intents, Checkout Sessions, refunds, subscriptions, idempotency, and webhook verification.

```bash
composer install
composer test
```

Configure `STRIPE_SECRET`, `STRIPE_WEBHOOK_SECRET`, and optionally `STRIPE_API_VERSION`. Values belong in the environment only.

```php
$intent = $stripe->createPaymentIntent(4999, 'usd', ['order_id' => '1001']);
```

Amounts use the currency's smallest unit. Never trust client-supplied totals, and verify webhook signatures before changing payment state.
