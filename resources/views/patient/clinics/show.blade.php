<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $clinic->name }}</h2></x-slot>
    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @include('partials.flash')
            <p class="text-sm text-gray-600">{{ $clinic->address }}</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @forelse ($doctors as $doctor)
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <div class="font-semibold text-gray-900">{{ $doctor->user->name }}</div>
                        <div class="text-sm text-gray-600">{{ $doctor->specialisation }} · ₹{{ number_format($doctor->consultation_fee, 0) }}</div>
                        <a href="{{ route('patient.slots.index', $doctor) }}" class="inline-block mt-3 text-sm bg-gray-800 text-white px-3 py-2 rounded-md">View slots</a>
                    </div>
                @empty
                    <p class="text-gray-500">No doctors at this clinic yet.</p>
                @endforelse
            </div>
            <a href="{{ route('patient.clinics.index') }}" class="text-sm text-gray-600 underline">Back to clinics</a>
        </div>
    </div>
</x-app-layout>
