<?php

namespace App\Services\GitHub;

use App\Models\GitHubConnection;
use Illuminate\Support\Facades\Http;

class GitHubService
{
    public function testConnection(GitHubConnection $connection): array
    {
        $response = Http::baseUrl('https://api.github.com')
            ->acceptJson()
            ->withToken($connection->access_token)
            ->withHeaders([
                'X-GitHub-Api-Version' => '2022-11-28',
                'User-Agent' => config('app.name', 'ReleaseLane'),
            ])
            ->timeout(15)
            ->get('/user');

        if ($response->failed()) {
            return [
                'success' => false,
                'status' => $response->status(),
                'message' => 'GitHub connection failed.',
                'data' => $response->json(),
            ];
        }

        return [
            'success' => true,
            'status' => $response->status(),
            'message' => 'GitHub connection successful.',
            'data' => $response->json(),
        ];
    }
}