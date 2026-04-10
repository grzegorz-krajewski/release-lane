<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Repositories
            </h2>

            <form method="POST" action="{{ route('repositories.sync') }}">
                @csrf
                <button
                    type="submit"
                    class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800"
                >
                    Sync repositories
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    @if ($repositories->isEmpty())
                        <p class="text-sm text-gray-600">
                            No repositories synced yet.
                        </p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr class="text-left text-sm font-semibold text-gray-700">
                                        <th class="px-4 py-3">Repository</th>
                                        <th class="px-4 py-3">Language</th>
                                        <th class="px-4 py-3">Private</th>
                                        <th class="px-4 py-3">Fork</th>
                                        <th class="px-4 py-3">Updated</th>
                                        <th class="px-4 py-3">Link</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                                    @foreach ($repositories as $repository)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <div class="font-medium">{{ $repository->full_name }}</div>
                                                @if ($repository->description)
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ $repository->description }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">{{ $repository->language ?? '—' }}</td>
                                            <td class="px-4 py-3">{{ $repository->is_private ? 'Yes' : 'No' }}</td>
                                            <td class="px-4 py-3">{{ $repository->is_fork ? 'Yes' : 'No' }}</td>
                                            <td class="px-4 py-3">
                                                {{ $repository->github_updated_at?->diffForHumans() ?? '—' }}
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