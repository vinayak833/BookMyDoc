<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin Dashboard</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @include('partials.flash')
            <p class="text-gray-700">Welcome, {{ auth()->user()->name }}.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('admin.clinics.index') }}" class="block bg-white shadow-sm sm:rounded-lg p-6 hover:shadow-md"><div class="font-semibold text-gray-900">Clinics</div><div class="mt-1 text-sm text-gray-600">Add and edit clinics.</div></a>
                <a href="{{ route('admin.doctors.index') }}" class="block bg-white shadow-sm sm:rounded-lg p-6 hover:shadow-md"><div class="font-semibold text-gray-900">Doctors</div><div class="mt-1 text-sm text-gray-600">Manage doctors and their accounts.</div></a>
                <a href="{{ route('admin.slots.index') }}" class="block bg-white shadow-sm sm:rounded-lg p-6 hover:shadow-md"><div class="font-semibold text-gray-900">Time slots</div><div class="mt-1 text-sm text-gray-600">View slots and generate new ones.</div></a>
            </div>
        </div>
    </div>
</x-app-layout>
