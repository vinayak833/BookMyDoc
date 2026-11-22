<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Choose a clinic</h2></x-slot>
    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @include('partials.flash')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @forelse ($clinics as $clinic)
                    <a href="{{ route('patient.clinics.show', $clinic) }}" class="block bg-white shadow-sm sm:rounded-lg p-6 hover:shadow-md">
                        <div class="font-semibold text-gray-900">{{ $clinic->name }}</div>
                        <div class="mt-1 text-sm text-gray-600">{{ $clinic->address }}</div>
                        <div class="mt-2 text-xs text-gray-500">{{ $clinic->doctors_count }} doctor(s)</div>
                    </a>
                @empty
                    <p class="text-gray-500">No clinics available right now.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
