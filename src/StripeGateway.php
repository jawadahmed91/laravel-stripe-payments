<?php

namespace JawadAhmed\LaravelStripe;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

final class StripeGateway
{
    public function __construct(
        private readonly string $secret,
        private readonly ?string $apiVersion = null,
    ) {}

    /** @param array<string, string> $metadata @return array<string, mixed> */
    public function createPaymentIntent(int $amount, string $currency = 'usd', array $metadata = []): array
    {
        return $this->api()->asForm()->withHeaders(['Idempotency-Key' => bin2hex(random_bytes(16))])
            ->post('/v1/payment_intents', [
                'amount' => $amount,
                'currency' => strtolower($currency),
                'automatic_payment_methods[enabled]' => 'true',
                ...$this->flattenMetadata($metadata),
            ])->throw()->json();
    }

    /** @return array<string, mixed> */
    public function refund(string $paymentIntentId, ?int $amount = null): array
    {
        return $this->api()->asForm()->post('/v1/refunds', array_filter([
            'payment_intent' => $paymentIntentId,
            'amount' => $amount,
        ], static fn (mixed $value): bool => $value !== null))->throw()->json();
    }

    public function verifyWebhook(string $payload, string $signature, string $webhookSecret, int $tolerance = 300): bool
    {
        $parts = collect(explode(',', $signature))->mapWithKeys(function (string $part): array {
            [$key, $value] = array_pad(explode('=', $part, 2), 2, '');
            return [$key => $value];
        });

        $timestamp = (int) $parts->get('t');
        $expected = hash_hmac('sha256', $timestamp.'.'.$payload, $webhookSecret);

        return abs(time() - $timestamp) <= $tolerance && hash_equals($expected, (string) $parts->get('v1'));
    }

    private function api(): PendingRequest
    {
        return Http::baseUrl('https://api.stripe.com')->withToken($this->secret)->acceptJson()
            ->when($this->apiVersion, fn (PendingRequest $request) => $request->withHeaders(['Stripe-Version' => $this->apiVersion]));
    }

    /** @param array<string, string> $metadata @return array<string, string> */
    private function flattenMetadata(array $metadata): array
    {
        $result = [];
        foreach ($metadata as $key => $value) {
            $result["metadata[{$key}]"] = $value;
        }
        return $result;
    }
}
