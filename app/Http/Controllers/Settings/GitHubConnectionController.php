<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateGitHubConnectionRequest;
use App\Models\GitHubConnection;
use App\Services\GitHub\GitHubService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GitHubConnectionController extends Controller
{
    public function edit(): View
    {
        $connection = auth()->user()->githubConnection;

        return view('settings.github', [
            'connection' => $connection,
        ]);
    }

    public function update(UpdateGitHubConnectionRequest $request): RedirectResponse
    {
        auth()->user()->githubConnection()->updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'access_token' => $request->string('access_token')->toString(),
                'is_active' => $request->boolean('is_active', true),
            ]
        );

        return redirect()
            ->route('settings.github.edit')
            ->with('success', 'GitHub connection saved.');
    }

    public function test(GitHubService $gitHubService): RedirectResponse
    {
        /** @var GitHubConnection|null $connection */
        $connection = auth()->user()->githubConnection;

        if (! $connection) {
            return redirect()
                ->route('settings.github.edit')
                ->with('error', 'Save your GitHub connection first.');
        }

        $result = $gitHubService->testConnection($connection);

        if (! $result['success']) {
            return redirect()
                ->route('settings.github.edit')
                ->with('error', 'GitHub test failed. Check token permissions.');
        }

        $connection->update([
            'last_tested_at' => now(),
        ]);

        $login = data_get($result, 'data.login');
        $name = data_get($result, 'data.name') ?: $login;

        return redirect()
            ->route('settings.github.edit')
            ->with('success', "Connection successful: {$name} (@{$login})");
    }
}