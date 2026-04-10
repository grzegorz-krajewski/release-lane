<?php

namespace App\Http\Controllers;

use App\Actions\GitHub\SyncRepositoriesAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RepositoryController extends Controller
{
    public function index(): View
    {
        $repositories = auth()
            ->user()
            ->repositories()
            ->latest('github_pushed_at')
            ->get();

        return view('repositories.index', [
            'repositories' => $repositories,
        ]);
    }

    public function sync(SyncRepositoriesAction $syncRepositoriesAction): RedirectResponse
    {
        $count = $syncRepositoriesAction->execute(auth()->user());

        return redirect()
            ->route('repositories.index')
            ->with('success', "Repositories synced: {$count}");
    }
}