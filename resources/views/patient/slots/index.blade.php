<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $doctor->user->name }}</h2></x-slot>
    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @include('partials.flash')
            <p class="text-sm text-gray-600">{{ $doctor->specialisation }} · {{ $doctor->clinic->name }} · ₹{{ number_format($doctor->consultation_fee, 0) }}</p>

            <form method="GET" class="flex items-end gap-3">
                <div>
                    <label class="block text-sm text-gray-600" for="date">Date</label>
                    <input id="date" type="date" name="date" value="{{ $date }}" min="{{ now()->toDateString() }}" class="border-gray-300 rounded-md shadow-sm">
                </div>
                <x-primary-button>Show slots</x-primary-button>
            </form>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if ($slots->isEmpty())
                    <p class="text-gray-500">No free slots on this date. Try another day.</p>
                @else
                    <form method="POST" action="{{ route('patient.appointments.store') }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                            @foreach ($slots as $slot)
                                <label class="cursor-pointer">
                                    <input type="radio" name="time_slot_id" value="{{ $slot->id }}" class="peer sr-only" required>
                                    <span class="block text-center text-sm border rounded-md py-2 peer-checked:bg-indigo-600 peer-checked:text-white">{{ $slot->slot_start->format('h:i A') }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div>
                            <x-input-label for="chief_complaint" value="Reason for visit (optional)" />
                            <textarea id="chief_complaint" name="chief_complaint" rows="3" maxlength="500" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('chief_complaint') }}</textarea>
                        </div>
                        <x-primary-button>Book appointment</x-primary-button>
                    </form>
                @endif
            </div>
            <a href="{{ route('patient.clinics.show', $doctor->clinic_id) }}" class="text-sm text-gray-600 underline">Back to doctors</a>
        </div>
    </div>
</x-app-layout>
