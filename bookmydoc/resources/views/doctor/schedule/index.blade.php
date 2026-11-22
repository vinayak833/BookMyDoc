<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">My schedule</h2></x-slot>
    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @include('partials.flash')
            <form method="GET" class="flex items-end gap-3">
                <div>
                    <label class="block text-sm text-gray-600" for="date">Date</label>
                    <input id="date" type="date" name="date" value="{{ $date }}" class="border-gray-300 rounded-md shadow-sm">
                </div>
                <x-primary-button>Show</x-primary-button>
            </form>

            <div class="overflow-x-auto bg-white shadow-sm sm:rounded-lg">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr><th class="p-3">Time</th><th class="p-3">Patient</th><th class="p-3">Reason</th><th class="p-3">Status</th><th class="p-3"></th></tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($appointments as $appointment)
                            <tr>
                                <td class="p-3 whitespace-nowrap">{{ $appointment->scheduled_at->format('h:i A') }}</td>
                                <td class="p-3">{{ $appointment->patient->user->name }}</td>
                                <td class="p-3">{{ $appointment->chief_complaint ?: '—' }}</td>
                                <td class="p-3">{{ $appointment->status }}</td>
                                <td class="p-3 whitespace-nowrap text-right">
                                    @if ($appointment->status === 'Scheduled')
                                        @foreach (['Completed', 'No-Show'] as $status)
                                            <form method="POST" action="{{ route('doctor.appointments.status', $appointment) }}" class="inline">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="{{ $status }}">
                                                <button class="text-indigo-600 ml-2">Mark {{ $status }}</button>
                                            </form>
                                        @endforeach
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-6 text-center text-gray-500">No appointments on this date.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
