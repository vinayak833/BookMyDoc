<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Generate time slots</h2></x-slot>
    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @include('partials.flash')
            <form method="POST" action="{{ route('admin.slots.store') }}" class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                @csrf
                <div>
                    <x-input-label for="doctor_id" value="Doctor" />
                    <select id="doctor_id" name="doctor_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        @foreach ($doctors as $doctor)
                            <option value="{{ $doctor->id }}" @selected(old('doctor_id') == $doctor->id)>{{ $doctor->user->name }} ({{ $doctor->clinic->name }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="date_from" value="From date" />
                        <x-text-input id="date_from" type="date" name="date_from" class="mt-1 block w-full" :value="old('date_from', now()->toDateString())" required />
                    </div>
                    <div>
                        <x-input-label for="date_to" value="To date" />
                        <x-text-input id="date_to" type="date" name="date_to" class="mt-1 block w-full" :value="old('date_to', now()->toDateString())" required />
                    </div>
                    <div>
                        <x-input-label for="start_time" value="Start time" />
                        <x-text-input id="start_time" type="time" name="start_time" class="mt-1 block w-full" :value="old('start_time', '09:00')" required />
                    </div>
                    <div>
                        <x-input-label for="end_time" value="End time" />
                        <x-text-input id="end_time" type="time" name="end_time" class="mt-1 block w-full" :value="old('end_time', '13:00')" required />
                    </div>
                </div>
                <div>
                    <x-input-label for="duration" value="Slot length (minutes)" />
                    <select id="duration" name="duration" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @foreach ([10, 15, 20, 30, 45, 60] as $m)
                            <option value="{{ $m }}" @selected(old('duration', 30) == $m)>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <p class="text-sm text-gray-500">Existing slots are never duplicated. Up to 31 days at a time.</p>
                <div class="flex items-center gap-3">
                    <x-primary-button>Generate</x-primary-button>
                    <a href="{{ route('admin.slots.index') }}" class="text-sm text-gray-600 underline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
