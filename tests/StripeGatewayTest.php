<?php

namespace Tests;

use Illuminate\Support\Facades\Http;
use JawadAhmed\LaravelStripe\StripeGateway;
use Orchestra\Testbench\TestCase;

final class StripeGatewayTest extends TestCase
{
    public function test_it_creates_a_payment_intent(): void
    {
        Http::fake(['api.stripe.com/v1/payment_intents' => Http::response(['id' => 'pi_123'])]);

        $result = (new StripeGateway('sk_test'))->createPaymentIntent(4999, 'USD', ['order_id' => '1001']);

        self::assertSame('pi_123', $result['id']);
        Http::assertSent(fn ($request) => $request['amount'] === 4999
            && $request['currency'] === 'usd'
            && $request['metadata[order_id]'] === '1001');
    }
}
