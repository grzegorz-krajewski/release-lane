<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Workflow Runs
            </h2>

            <form method="POST" action="{{ route('workflow-runs.sync') }}">
                @csrf
                <button
                    type="submit"
                    class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800"
                >
                    Sync workflow runs
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    @if ($workflowRuns->isEmpty())
                        <p class="text-sm text-gray-600">
                            No workflow runs synced yet.
                        </p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr class="text-left text-sm font-semibold text-gray-700">
                                        <th class="px-4 py-3">Workflow</th>
                                        <th class="px-4 py-3">Repository</th>
                                        <th class="px-4 py-3">Status</th>
                                        <th class="px-4 py-3">Branch</th>
                                        <th class="px-4 py-3">Event</th>
                                        <th class="px-4 py-3">Updated</th>
                                        <th class="px-4 py-3">Link</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                                    @foreach ($workflowRuns as $workflowRun)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <div class="font-medium">
                                                    {{ $workflowRun->name ?? 'Unnamed workflow' }}
                                                </div>
                                                @if ($workflowRun->display_title)
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ $workflowRun->display_title }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                {{ $workflowRun->repository?->full_name ?? '—' }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <div>{{ $workflowRun->status ?? '—' }}</div>
                                                @if ($workflowRun->conclusion)
                                                    <div class="text-xs text-gray-500">
                                                        {{ $workflowRun->conclusion }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                {{ $workflowRun->head_branch ?? '—' }}
                                            </td>
                                            <td class="px-4 py-3">
                                                {{ $workflowRun->event ?? '—' }}
                                            </td>
                                            <td class="px-4 py-3">
                                                {{ $workflowRun->github_updated_at?->diffForHumans() ?? '—' }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <a
                                                    href="{{ $workflowRun->html_url }}"
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