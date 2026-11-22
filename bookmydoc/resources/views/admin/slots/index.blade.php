<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Time slots</h2>
            <a href="{{ route('admin.slots.create') }}" class="text-sm bg-gray-800 text-white px-3 py-2 rounded-md">Generate slots</a>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @include('partials.flash')
            <form method="GET" class="bg-white shadow-sm sm:rounded-lg p-4 flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-sm text-gray-600" for="doctor_id">Doctor</label>
                    <select id="doctor_id" name="doctor_id" class="border-gray-300 rounded-md shadow-sm">
                        <option value="">All doctors</option>
                        @foreach ($doctors as $doctor)
                            <option value="{{ $doctor->id }}" @selected(request('doctor_id') == $doctor->id)>{{ $doctor->user->name }} ({{ $doctor->clinic->name }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-600" for="date">Date</label>
                    <input id="date" type="date" name="date" value="{{ request('date') }}" class="border-gray-300 rounded-md shadow-sm">
                </div>
                <x-primary-button>Filter</x-primary-button>
            </form>
            <div class="overflow-x-auto bg-white shadow-sm sm:rounded-lg">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr><th class="p-3">Doctor</th><th class="p-3">Clinic</th><th class="p-3">Start</th><th class="p-3">End</th><th class="p-3">Status</th><th class="p-3"></th></tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($slots as $slot)
                            <tr>
                                <td class="p-3">{{ $slot->doctor->user->name }}</td>
                                <td class="p-3">{{ $slot->doctor->clinic->name }}</td>
                                <td class="p-3 whitespace-nowrap">{{ $slot->slot_start->format('d M Y, h:i A') }}</td>
                                <td class="p-3 whitespace-nowrap">{{ $slot->slot_end->format('h:i A') }}</td>
                                <td class="p-3">{{ $slot->is_booked ? 'Booked' : 'Free' }}</td>
                                <td class="p-3 text-right">
                                    @unless ($slot->is_booked)
                                        <form method="POST" action="{{ route('admin.slots.destroy', $slot) }}" onsubmit="return confirm('Delete this slot?')">
                                            @csrf @method('DELETE')
                                            <button class="text-red-600">Delete</button>
                                        </form>
                                    @endunless
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-6 text-center text-gray-500">No slots found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $slots->links() }}
        </div>
    </div>
</x-app-layout>
