<?php

namespace App\Support\Webhooks;

class GitHubWebhookSignatureValidator
{
    public function isValid(string $payload, ?string $signature, ?string $secret): bool
    {
        if (! $signature || ! $secret) {
            return false;
        }

        $expected = 'sha256=' . hash_hmac('sha256', $payload, $secret);

        return hash_equals($expected, $signature);
    }
}