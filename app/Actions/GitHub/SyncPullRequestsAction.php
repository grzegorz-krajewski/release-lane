<?php

namespace App\Actions\GitHub;

use App\Models\PullRequest;
use App\Models\Repository;
use App\Models\User;
use App\Services\GitHub\GitHubService;

class SyncPullRequestsAction
{
    public function __construct(
        protected GitHubService $gitHubService
    ) {}

    public function execute(User $user): int
    {
        $connection = $user->githubConnection;

        if (! $connection || ! $connection->is_active) {
            return 0;
        }

        $count = 0;

        foreach ($user->repositories as $repository) {
            $result = $this->gitHubService->fetchPullRequests(
                $connection,
                $repository->owner_login,
                $repository->name
            );

            if (! $result['success']) {
                continue;
            }

            foreach ($result['data'] as $pr) {
                PullRequest::updateOrCreate(
                    [
                        'github_pull_request_id' => $pr['id'],
                    ],
                    [
                        'user_id' => $user->id,
                        'repository_id' => $repository->id,
                        'github_number' => $pr['number'],
                        'title' => $pr['title'],
                        'state' => $pr['state'],
                        'is_draft' => $pr['draft'] ?? false,
                        'author_login' => data_get($pr, 'user.login'),
                        'author_avatar_url' => data_get($pr, 'user.avatar_url'),
                        'source_branch' => data_get($pr, 'head.ref'),
                        'target_branch' => data_get($pr, 'base.ref'),
                        'html_url' => $pr['html_url'],
                        'api_url' => $pr['url'] ?? null,
                        'github_created_at' => $pr['created_at'] ?? null,
                        'github_updated_at' => $pr['updated_at'] ?? null,
                        'github_closed_at' => $pr['closed_at'] ?? null,
                        'github_merged_at' => $pr['merged_at'] ?? null,
                        'last_synced_at' => now(),
                        'raw_payload' => $pr,
                    ]
                );

                $count++;
            }
        }

        return $count;
    }
}