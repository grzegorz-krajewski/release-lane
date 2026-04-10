<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            GitHub Connection
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Connection settings</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Configure your GitHub personal access token.
                    </p>
                </div>

                <form method="POST" action="{{ route('settings.github.update') }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="access_token" class="block text-sm font-medium text-gray-700">
                            Personal Access Token
                        </label>
                        <input
                            id="access_token"
                            name="access_token"
                            type="password"
                            required
                            value="{{ old('access_token') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            placeholder="github_pat_..."
                        >
                        <p class="mt-2 text-sm text-gray-500">
                            For safety, the token field is always empty on reload.
                        </p>
                        @error('access_token')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-2">
                        <input
                            id="is_active"
                            name="is_active"
                            type="checkbox"
                            value="1"
                            {{ old('is_active', $connection?->is_active ?? true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600 shadow-sm"
                        >
                        <label for="is_active" class="text-sm text-gray-700">
                            Connection is active
                        </label>
                    </div>

                    <div class="flex items-center gap-3">
                        <button
                            type="submit"
                            class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800"
                        >
                            Save connection
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Connection test</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Verify if the saved token can reach the GitHub API.
                    </p>
                </div>

                <div class="mb-4 text-sm text-gray-600">
                    <span class="font-medium">Last tested:</span>
                    {{ $connection?->last_tested_at?->diffForHumans() ?? 'Never' }}
                </div>

                <form method="POST" action="{{ route('settings.github.test') }}">
                    @csrf

                    <button
                        type="submit"
                        class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500"
                    >
                        Test connection
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>