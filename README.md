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

## Add this to an existing Laravel project

1. Copy `src/StripeGateway.php` to `app/Payments/Stripe/StripeGateway.php` and change its namespace to `App\Payments\Stripe`.
2. Copy `config/stripe.php` into your application's `config` directory.
3. Add `STRIPE_SECRET`, `STRIPE_WEBHOOK_SECRET`, and optionally `STRIPE_API_VERSION` to `.env`. Start with Stripe test-mode keys.
4. Bind `StripeGateway` as a singleton in a service provider using `config('stripe.secret')` and `config('stripe.api_version')`.
5. In the payment endpoint, load the order and calculate the amount and currency from server-side data before calling `createPaymentIntent()`.
6. Store the Payment Intent ID and return only the client secret required by Stripe.js. Let Stripe.js collect payment details so raw card data never reaches Laravel.
7. Replace the example's random idempotency key with a stable key stored per payment attempt before production use. Retrying the same attempt must reuse the same key.
8. Create a webhook endpoint that reads the raw request body and `Stripe-Signature` header, calls `verifyWebhook()`, rejects invalid requests, and processes each Stripe event ID only once.
9. Update payment state from verified webhook events rather than trusting the browser redirect. Store refund IDs and amounts when calling `refund()`.
10. Copy `tests/StripeGatewayTest.php` into your project, update its namespace and imports, and run `php artisan test`.
11. Complete Payment Intent, webhook, failed-payment, duplicate-request, and refund scenarios in test mode before using live keys.
