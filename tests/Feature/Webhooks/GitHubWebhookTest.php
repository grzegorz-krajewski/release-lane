<?php

namespace Tests\Feature\Webhooks;

use App\Actions\GitHub\SyncPullRequestsAction;
use App\Actions\GitHub\SyncWorkflowRunsAction;
use App\Models\Repository;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class GitHubWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.github.webhook_secret', 'test-secret');
    }

    public function test_it_rejects_webhook_with_invalid_signature(): void
    {
        $payload = json_encode([
            'repository' => [
                'full_name' => 'grzegorz-krajewski/release-lane',
            ],
        ], JSON_THROW_ON_ERROR);

        $response = $this->withHeaders([
            'X-GitHub-Event' => 'ping',
            'X-GitHub-Delivery' => 'delivery-invalid',
            'X-Hub-Signature-256' => 'sha256=invalid',
            'Content-Type' => 'application/json',
        ])->postJson('/webhooks/github', json_decode($payload, true, 512, JSON_THROW_ON_ERROR));

        $response->assertStatus(401);

        $this->assertDatabaseCount('webhook_events', 0);
    }

    public function test_it_stores_webhook_event_with_valid_signature(): void
    {
        $user = User::factory()->create();

        Repository::create([
            'user_id' => $user->id,
            'github_repository_id' => 1001,
            'name' => 'release-lane',
            'full_name' => 'grzegorz-krajewski/release-lane',
            'owner_login' => 'grzegorz-krajewski',
            'description' => 'Repo',
            'is_private' => false,
            'is_fork' => false,
            'default_branch' => 'main',
            'html_url' => 'https://github.com/grzegorz-krajewski/release-lane',
            'api_url' => 'https://api.github.com/repos/grzegorz-krajewski/release-lane',
            'language' => 'PHP',
            'stargazers_count' => 0,
            'watchers_count' => 0,
            'forks_count' => 0,
            'last_synced_at' => now(),
            'raw_payload' => [],
        ]);

        $payloadArray = [
            'action' => 'opened',
            'repository' => [
                'full_name' => 'grzegorz-krajewski/release-lane',
            ],
        ];

        $payload = json_encode($payloadArray, JSON_THROW_ON_ERROR);
        $signature = 'sha256=' . hash_hmac('sha256', $payload, 'test-secret');

        $syncPullRequests = Mockery::mock(SyncPullRequestsAction::class);
        $syncPullRequests->shouldReceive('syncRepository')->once();
        $this->app->instance(SyncPullRequestsAction::class, $syncPullRequests);

        $syncWorkflowRuns = Mockery::mock(SyncWorkflowRunsAction::class);
        $syncWorkflowRuns->shouldNotReceive('syncRepository');
        $this->app->instance(SyncWorkflowRunsAction::class, $syncWorkflowRuns);

        $response = $this->withHeaders([
            'X-GitHub-Event' => 'pull_request',
            'X-GitHub-Delivery' => 'delivery-valid',
            'X-Hub-Signature-256' => $signature,
            'Content-Type' => 'application/json',
        ])->postJson('/webhooks/github', $payloadArray);

        $response->assertOk();

        $this->assertDatabaseHas('webhook_events', [
            'provider' => 'github',
            'event_type' => 'pull_request',
            'action' => 'opened',
            'delivery_id' => 'delivery-valid',
            'repository_full_name' => 'grzegorz-krajewski/release-lane',
            'is_valid_signature' => true,
            'user_id' => $user->id,
        ]);
    }
}