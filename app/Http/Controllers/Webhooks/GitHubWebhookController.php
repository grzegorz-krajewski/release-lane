<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Repository;
use App\Models\User;
use App\Models\WebhookEvent;
use App\Support\Webhooks\GitHubWebhookSignatureValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Actions\GitHub\SyncPullRequestsAction;
use App\Actions\GitHub\SyncWorkflowRunsAction;

class GitHubWebhookController extends Controller
{
    public function __invoke(
        Request $request,
        GitHubWebhookSignatureValidator $validator,
        SyncPullRequestsAction $syncPullRequestsAction,
        SyncWorkflowRunsAction $syncWorkflowRunsAction
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

        if ($repository && $user) {
            match ($eventType) {
                'pull_request' => $syncPullRequestsAction->syncRepository($user, $repository),
                'workflow_run' => $syncWorkflowRunsAction->syncRepository($user, $repository),
                'push' => $repository->update([
                    'last_synced_at' => now(),
                    'github_pushed_at' => data_get($decoded, 'repository.pushed_at', $repository->github_pushed_at),
                    'raw_payload' => $repository->raw_payload,
                ]),
                default => null,
            };

            if ($eventType === 'push' && $repository) {
                $repository->update([
                    'last_synced_at' => now(),
                ]);
            }
        }

        return response()->json([
            'message' => 'Webhook received.',
        ]);
    }
}