<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">My appointments</h2></x-slot>
    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @include('partials.flash')
            <div class="overflow-x-auto bg-white shadow-sm sm:rounded-lg">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr><th class="p-3">When</th><th class="p-3">Doctor</th><th class="p-3">Clinic</th><th class="p-3">Status</th><th class="p-3"></th></tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($appointments as $appointment)
                            <tr>
                                <td class="p-3 whitespace-nowrap">{{ $appointment->scheduled_at?->format('d M Y, h:i A') }}</td>
                                <td class="p-3">{{ $appointment->doctor->user->name }}</td>
                                <td class="p-3">{{ $appointment->clinic->name }}</td>
                                <td class="p-3">{{ $appointment->status }}</td>
                                <td class="p-3 text-right whitespace-nowrap">
                                    @if ($appointment->status !== 'Cancelled')
                                        <a href="{{ route('appointments.pdf', $appointment) }}" class="text-indigo-600 mr-3">PDF</a>
                                    @endif
                                    @if ($appointment->status === 'Scheduled')
                                        <form method="POST" action="{{ route('patient.appointments.cancel', $appointment) }}" class="inline" onsubmit="return confirm('Cancel this appointment?')">
                                            @csrf @method('PATCH')
                                            <button class="text-red-600">Cancel</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-6 text-center text-gray-500">You have no appointments yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <a href="{{ route('patient.clinics.index') }}" class="inline-block text-sm bg-gray-800 text-white px-3 py-2 rounded-md">Book another appointment</a>
        </div>
    </div>
</x-app-layout>
