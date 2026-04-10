<?php

namespace App\Actions\GitHub;

use App\Models\User;
use App\Models\WorkflowRun;
use App\Services\GitHub\GitHubService;

class SyncWorkflowRunsAction
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
            $result = $this->gitHubService->fetchWorkflowRuns(
                $connection,
                $repository->owner_login,
                $repository->name
            );

            if (! $result['success']) {
                continue;
            }

            foreach ($result['data'] as $run) {
                WorkflowRun::updateOrCreate(
                    [
                        'github_workflow_run_id' => $run['id'],
                    ],
                    [
                        'user_id' => $user->id,
                        'repository_id' => $repository->id,
                        'github_workflow_id' => $run['workflow_id'] ?? null,
                        'name' => $run['name'] ?? null,
                        'display_title' => $run['display_title'] ?? null,
                        'run_number' => $run['run_number'] ?? null,
                        'run_attempt' => $run['run_attempt'] ?? null,
                        'status' => $run['status'] ?? null,
                        'conclusion' => $run['conclusion'] ?? null,
                        'event' => $run['event'] ?? null,
                        'head_branch' => $run['head_branch'] ?? null,
                        'head_sha' => $run['head_sha'] ?? null,
                        'actor_login' => data_get($run, 'actor.login'),
                        'html_url' => $run['html_url'],
                        'api_url' => $run['url'] ?? null,
                        'run_started_at' => $run['run_started_at'] ?? null,
                        'github_created_at' => $run['created_at'] ?? null,
                        'github_updated_at' => $run['updated_at'] ?? null,
                        'last_synced_at' => now(),
                        'raw_payload' => $run,
                    ]
                );

                $count++;
            }
        }

        return $count;
    }
}