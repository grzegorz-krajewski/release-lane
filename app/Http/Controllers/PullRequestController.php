<?php

namespace App\Http\Controllers;

use App\Actions\GitHub\SyncPullRequestsAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PullRequestController extends Controller
{
    public function index(): View
    {
        $pullRequests = auth()
            ->user()
            ->pullRequests()
            ->with('repository')
            ->latest('github_updated_at')
            ->get();

        return view('pull-requests.index', [
            'pullRequests' => $pullRequests,
        ]);
    }

    public function sync(SyncPullRequestsAction $syncPullRequestsAction): RedirectResponse
    {
        $count = $syncPullRequestsAction->execute(auth()->user());

        return redirect()
            ->route('pull-requests.index')
            ->with('success', "Pull requests synced: {$count}");
    }
}