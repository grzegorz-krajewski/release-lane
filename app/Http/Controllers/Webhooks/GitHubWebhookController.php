<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Repository;
use App\Models\User;
use App\Models\WebhookEvent;
use App\Support\Webhooks\GitHubWebhookSignatureValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GitHubWebhookController extends Controller
{
    public function __invoke(
        Request $request,
        GitHubWebhookSignatureValidator $validator
    ): JsonResponse {
        $payload = $request->getContent();
        $signature = $request->header('X-Hub-Signature-256');
        $eventType = $request->header('X-GitHub-Event', 'unknown');
        $deliveryId = $request->header('X-GitHub-Delivery');

        $secret = config('services.github.webhook_secret');
        $isValid = $validator->isValid($payload, $signature, $secret);

        if (! $isValid) {
            return response()->json([
                'message' => 'Invalid signature.',
            ], 401);
        }

        $decoded = $request->json()->all();
        $repositoryFullName = data_get($decoded, 'repository.full_name');
        $repository = null;
        $user = null;

        if ($repositoryFullName) {
            $repository = Repository::query()
                ->where('full_name', $repositoryFullName)
                ->first();

            $user = $repository?->user;
        }

        WebhookEvent::updateOrCreate(
            [
                'delivery_id' => $deliveryId,
            ],
            [
                'user_id' => $user?->id,
                'repository_id' => $repository?->id,
                'provider' => 'github',
                'event_type' => $eventType,
                'action' => data_get($decoded, 'action'),
                'repository_full_name' => $repositoryFullName,
                'is_valid_signature' => true,
                'received_at' => now(),
                'headers' => [
                    'x-github-event' => $eventType,
                    'x-github-delivery' => $deliveryId,
                    'x-hub-signature-256' => $signature,
                ],
                'payload' => $decoded,
            ]
        );

        return response()->json([
            'message' => 'Webhook received.',
        ]);
    }
}