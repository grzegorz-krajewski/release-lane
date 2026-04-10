<?php

namespace App\Http\Controllers;

use App\Actions\GitHub\SyncWorkflowRunsAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WorkflowRunController extends Controller
{
    public function index(): View
    {
        $workflowRuns = auth()
            ->user()
            ->workflowRuns()
            ->with('repository')
            ->latest('github_updated_at')
            ->get();

        return view('workflow-runs.index', [
            'workflowRuns' => $workflowRuns,
        ]);
    }

    public function sync(SyncWorkflowRunsAction $syncWorkflowRunsAction): RedirectResponse
    {
        $count = $syncWorkflowRunsAction->execute(auth()->user());

        return redirect()
            ->route('workflow-runs.index')
            ->with('success', "Workflow runs synced: {$count}");
    }
}