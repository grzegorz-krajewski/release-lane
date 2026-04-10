<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Webhook Events
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    @if ($events->isEmpty())
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
                                    @foreach ($events as $event)
                                        <tr>
                                            <td class="px-4 py-3">{{ $event->event_type }}</td>
                                            <td class="px-4 py-3">{{ $event->action ?? '—' }}</td>
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
        </div>
    </div>
</x-app-layout>