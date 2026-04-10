<?php

namespace Tests\Feature\GitHub;

use App\Actions\GitHub\SyncRepositoriesAction;
use App\Models\GitHubConnection;
use App\Models\Repository;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RepositorySyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_syncs_github_repositories_for_user(): void
    {
        $user = User::factory()->create();

        GitHubConnection::create([
            'user_id' => $user->id,
            'access_token' => 'fake-token',
            'is_active' => true,
        ]);

        Http::fake([
            'https://api.github.com/user/repos*' => Http::response([
                [
                    'id' => 1001,
                    'name' => 'release-lane',
                    'full_name' => 'grzegorz-krajewski/release-lane',
                    'owner' => [
                        'login' => 'grzegorz-krajewski',
                    ],
                    'description' => 'ReleaseLane repository',
                    'private' => false,
                    'fork' => false,
                    'default_branch' => 'main',
                    'html_url' => 'https://github.com/grzegorz-krajewski/release-lane',
                    'url' => 'https://api.github.com/repos/grzegorz-krajewski/release-lane',
                    'language' => 'PHP',
                    'stargazers_count' => 5,
                    'watchers_count' => 5,
                    'forks_count' => 1,
                    'created_at' => '2026-04-01T10:00:00Z',
                    'updated_at' => '2026-04-10T10:00:00Z',
                    'pushed_at' => '2026-04-10T10:30:00Z',
                ],
            ], 200),
        ]);

        $count = app(SyncRepositoriesAction::class)->execute($user);

        $this->assertSame(1, $count);

        $this->assertDatabaseHas('repositories', [
            'user_id' => $user->id,
            'github_repository_id' => 1001,
            'name' => 'release-lane',
            'full_name' => 'grzegorz-krajewski/release-lane',
            'owner_login' => 'grzegorz-krajewski',
            'language' => 'PHP',
        ]);
    }

    public function test_it_updates_existing_repository_instead_of_creating_duplicate(): void
    {
        $user = User::factory()->create();

        GitHubConnection::create([
            'user_id' => $user->id,
            'access_token' => 'fake-token',
            'is_active' => true,
        ]);

        Repository::create([
            'user_id' => $user->id,
            'github_repository_id' => 1001,
            'name' => 'release-lane',
            'full_name' => 'grzegorz-krajewski/release-lane',
            'owner_login' => 'grzegorz-krajewski',
            'description' => 'Old description',
            'is_private' => false,
            'is_fork' => false,
            'default_branch' => 'main',
            'html_url' => 'https://github.com/grzegorz-krajewski/release-lane',
            'api_url' => 'https://api.github.com/repos/grzegorz-krajewski/release-lane',
            'language' => 'PHP',
            'stargazers_count' => 1,
            'watchers_count' => 1,
            'forks_count' => 0,
            'last_synced_at' => now()->subDay(),
            'raw_payload' => [],
        ]);

        Http::fake([
            'https://api.github.com/user/repos*' => Http::response([
                [
                    'id' => 1001,
                    'name' => 'release-lane',
                    'full_name' => 'grzegorz-krajewski/release-lane',
                    'owner' => [
                        'login' => 'grzegorz-krajewski',
                    ],
                    'description' => 'New description',
                    'private' => false,
                    'fork' => false,
                    'default_branch' => 'main',
                    'html_url' => 'https://github.com/grzegorz-krajewski/release-lane',
                    'url' => 'https://api.github.com/repos/grzegorz-krajewski/release-lane',
                    'language' => 'PHP',
                    'stargazers_count' => 10,
                    'watchers_count' => 10,
                    'forks_count' => 2,
                    'created_at' => '2026-04-01T10:00:00Z',
                    'updated_at' => '2026-04-10T10:00:00Z',
                    'pushed_at' => '2026-04-10T10:30:00Z',
                ],
            ], 200),
        ]);

        app(SyncRepositoriesAction::class)->execute($user);

        $this->assertDatabaseCount('repositories', 1);

        $this->assertDatabaseHas('repositories', [
            'github_repository_id' => 1001,
            'description' => 'New description',
            'stargazers_count' => 10,
            'forks_count' => 2,
        ]);
    }
}