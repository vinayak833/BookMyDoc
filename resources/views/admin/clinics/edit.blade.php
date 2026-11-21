<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit clinic</h2></x-slot>
    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @include('partials.flash')
            <form method="POST" action="{{ route('admin.clinics.update', $clinic) }}" class="bg-white shadow-sm sm:rounded-lg p-6">
                @method('PUT')
                @include('admin.clinics._form')
            </form>
        </div>
    </div>
</x-app-layout>
