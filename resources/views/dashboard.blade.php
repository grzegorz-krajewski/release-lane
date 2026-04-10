<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Repositories</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $repositoriesCount }}</div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Open Pull Requests</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ $openPullRequestsCount }}</div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Failed Workflow Runs</div>
                    <div class="mt-2 text-3xl font-bold text-red-600">{{ $failedWorkflowRunsCount }}</div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">In Progress Runs</div>
                    <div class="mt-2 text-3xl font-bold text-indigo-600">{{ $inProgressWorkflowRunsCount }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Pull Requests</h3>
                    </div>

                    <div class="p-6">
                        @if ($recentPullRequests->isEmpty())
                            <p class="text-sm text-gray-600">No pull requests available.</p>
                        @else
                            <div class="space-y-4">
                                @foreach ($recentPullRequests as $pullRequest)
                                    <div class="rounded-lg border border-gray-100 p-4">
                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <div class="font-medium text-gray-900">
                                                    #{{ $pullRequest->github_number }} {{ $pullRequest->title }}
                                                </div>
                                                <div class="mt-1 text-sm text-gray-500">
                                                    {{ $pullRequest->repository?->full_name ?? '—' }}
                                                </div>
                                                <div class="mt-2 text-xs text-gray-500">
                                                    {{ $pullRequest->source_branch ?? '—' }} → {{ $pullRequest->target_branch ?? '—' }}
                                                </div>
                                            </div>

                                            <div class="text-right">
                                                <div class="text-sm font-medium text-gray-700">
                                                    {{ ucfirst($pullRequest->state) }}
                                                    @if ($pullRequest->is_draft)
                                                        <span class="text-xs text-gray-400">(draft)</span>
                                                    @endif
                                                </div>
                                                <div class="mt-1 text-xs text-gray-500">
                                                    {{ $pullRequest->github_updated_at?->diffForHumans() ?? '—' }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <a
                                                href="{{ $pullRequest->html_url }}"
                                                target="_blank"
                                                rel="noreferrer"
                                                class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
                                            >
                                                Open pull request
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Workflow Runs</h3>
                    </div>

                    <div class="p-6">
                        @if ($recentWorkflowRuns->isEmpty())
                            <p class="text-sm text-gray-600">No workflow runs available.</p>
                        @else
                            <div class="space-y-4">
                                @foreach ($recentWorkflowRuns as $workflowRun)
                                    <div class="rounded-lg border border-gray-100 p-4">
                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <div class="font-medium text-gray-900">
                                                    {{ $workflowRun->name ?? 'Unnamed workflow' }}
                                                </div>
                                                @if ($workflowRun->display_title)
                                                    <div class="mt-1 text-sm text-gray-500">
                                                        {{ $workflowRun->display_title }}
                                                    </div>
                                                @endif
                                                <div class="mt-1 text-sm text-gray-500">
                                                    {{ $workflowRun->repository?->full_name ?? '—' }}
                                                </div>
                                                <div class="mt-2 text-xs text-gray-500">
                                                    Branch: {{ $workflowRun->head_branch ?? '—' }} · Event: {{ $workflowRun->event ?? '—' }}
                                                </div>
                                            </div>

                                            <div class="text-right">
                                                <div class="text-sm font-medium text-gray-700">
                                                    {{ $workflowRun->status ?? '—' }}
                                                </div>
                                                @if ($workflowRun->conclusion)
                                                    <div class="mt-1 text-xs text-gray-500">
                                                        {{ $workflowRun->conclusion }}
                                                    </div>
                                                @endif
                                                <div class="mt-1 text-xs text-gray-500">
                                                    {{ $workflowRun->github_updated_at?->diffForHumans() ?? '—' }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <a
                                                href="{{ $workflowRun->html_url }}"
                                                target="_blank"
                                                rel="noreferrer"
                                                class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
                                            >
                                                Open workflow run
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Webhook Events</h3>
                </div>

                <div class="p-6">
                    @if ($recentWebhookEvents->isEmpty())
                        <p class="text-sm text-gray-600">No webhook events received yet.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr class="text-left text-sm font-semibold text-gray-700">
                                        <th class="px-4 py-3">Event</th>
                                        <th class="px-4 py-3">Action</th>
                                        <th class="px-4 py-3">Repository</th>
                                        <th class="px-4 py-3">Received</th>
                                        <th class="px-4 py-3">Delivery ID</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                                    @foreach ($recentWebhookEvents as $event)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <div class="font-medium">{{ $event->event_type }}</div>
                                            </td>
                                            <td class="px-4 py-3">
                                                {{ $event->action ?? '—' }}
                                            </td>
                                            <td class="px-4 py-3">
                                                {{ $event->repository?->full_name ?? $event->repository_full_name ?? '—' }}
                                            </td>
                                            <td class="px-4 py-3">
                                                {{ $event->received_at?->diffForHumans() ?? '—' }}
                                            </td>
                                            <td class="px-4 py-3 text-xs text-gray-500">
                                                {{ $event->delivery_id ?? '—' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recently Active Repositories</h3>
                </div>

                <div class="p-6">
                    @if ($recentRepositories->isEmpty())
                        <p class="text-sm text-gray-600">No repositories available.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr class="text-left text-sm font-semibold text-gray-700">
                                        <th class="px-4 py-3">Repository</th>
                                        <th class="px-4 py-3">Language</th>
                                        <th class="px-4 py-3">Default Branch</th>
                                        <th class="px-4 py-3">Last Push</th>
                                        <th class="px-4 py-3">Link</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                                    @foreach ($recentRepositories as $repository)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <div class="font-medium">{{ $repository->full_name }}</div>
                                                @if ($repository->description)
                                                    <div class="mt-1 text-xs text-gray-500">
                                                        {{ $repository->description }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">{{ $repository->language ?? '—' }}</td>
                                            <td class="px-4 py-3">{{ $repository->default_branch ?? '—' }}</td>
                                            <td class="px-4 py-3">
                                                {{ $repository->github_pushed_at?->diffForHumans() ?? '—' }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <a
                                                    href="{{ $repository->html_url }}"
                                                    target="_blank"
                                                    rel="noreferrer"
                                                    class="text-indigo-600 hover:text-indigo-800"
                                                >
                                                    Open
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>