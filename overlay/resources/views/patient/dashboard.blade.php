<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Patient Dashboard</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @include('partials.flash')
            <p class="text-gray-700">Welcome, {{ auth()->user()->name }}.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('patient.clinics.index') }}" class="block bg-white shadow-sm sm:rounded-lg p-6 hover:shadow-md"><div class="font-semibold text-gray-900">Book an appointment</div><div class="mt-1 text-sm text-gray-600">Choose a clinic, doctor and time slot.</div></a>
                <a href="{{ route('patient.appointments.index') }}" class="block bg-white shadow-sm sm:rounded-lg p-6 hover:shadow-md"><div class="font-semibold text-gray-900">My appointments</div><div class="mt-1 text-sm text-gray-600">View or cancel your bookings.</div></a>
            </div>
        </div>
    </div>
</x-app-layout>
