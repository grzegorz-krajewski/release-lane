<?php

namespace App\Actions\GitHub;

use App\Models\GitHubConnection;
use App\Models\Repository;
use App\Models\User;
use App\Services\GitHub\GitHubService;

class SyncRepositoriesAction
{
    public function __construct(
        protected GitHubService $gitHubService
    ) {}

    public function execute(User $user): int
    {
        /** @var GitHubConnection|null $connection */
        $connection = $user->githubConnection;

        if (! $connection || ! $connection->is_active) {
            return 0;
        }

        $result = $this->gitHubService->fetchRepositories($connection);

        if (! $result['success']) {
            return 0;
        }

        $count = 0;

        foreach ($result['data'] as $repo) {
            Repository::updateOrCreate(
                [
                    'github_repository_id' => $repo['id'],
                ],
                [
                    'user_id' => $user->id,
                    'name' => $repo['name'],
                    'full_name' => $repo['full_name'],
                    'owner_login' => data_get($repo, 'owner.login'),
                    'description' => $repo['description'],
                    'is_private' => $repo['private'],
                    'is_fork' => $repo['fork'],
                    'default_branch' => $repo['default_branch'] ?? null,
                    'html_url' => $repo['html_url'],
                    'api_url' => $repo['url'] ?? null,
                    'language' => $repo['language'] ?? null,
                    'stargazers_count' => $repo['stargazers_count'] ?? 0,
                    'watchers_count' => $repo['watchers_count'] ?? 0,
                    'forks_count' => $repo['forks_count'] ?? 0,
                    'github_created_at' => $repo['created_at'] ?? null,
                    'github_updated_at' => $repo['updated_at'] ?? null,
                    'github_pushed_at' => $repo['pushed_at'] ?? null,
                    'last_synced_at' => now(),
                    'raw_payload' => $repo,
                ]
            );

            $count++;
        }

        return $count;
    }
}