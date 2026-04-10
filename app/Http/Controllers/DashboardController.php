<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();

        $repositoriesCount = $user->repositories()->count();

        $openPullRequestsCount = $user->pullRequests()
            ->where('state', 'open')
            ->count();

        $failedWorkflowRunsCount = $user->workflowRuns()
            ->where('status', 'completed')
            ->where('conclusion', 'failure')
            ->count();

        $inProgressWorkflowRunsCount = $user->workflowRuns()
            ->whereIn('status', ['queued', 'in_progress', 'waiting', 'pending', 'requested'])
            ->count();

        $recentPullRequests = $user->pullRequests()
            ->with('repository')
            ->latest('github_updated_at')
            ->limit(8)
            ->get();

        $recentWorkflowRuns = $user->workflowRuns()
            ->with('repository')
            ->latest('github_updated_at')
            ->limit(8)
            ->get();

        $recentRepositories = $user->repositories()
            ->latest('github_pushed_at')
            ->limit(8)
            ->get();

        $recentWebhookEvents = $user->webhookEvents()
            ->with('repository')
            ->latest('received_at')
            ->limit(8)
            ->get();    

        return view('dashboard', [
            'repositoriesCount' => $repositoriesCount,
            'openPullRequestsCount' => $openPullRequestsCount,
            'failedWorkflowRunsCount' => $failedWorkflowRunsCount,
            'inProgressWorkflowRunsCount' => $inProgressWorkflowRunsCount,
            'recentPullRequests' => $recentPullRequests,
            'recentWorkflowRuns' => $recentWorkflowRuns,
            'recentRepositories' => $recentRepositories,
            'recentWebhookEvents' => $recentWebhookEvents,
        ]);
    }
}