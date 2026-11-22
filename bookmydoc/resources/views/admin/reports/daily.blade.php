<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Daily bookings report</h2></x-slot>
    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @include('partials.flash')

            <form method="GET" class="bg-white shadow-sm sm:rounded-lg p-4 flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-sm text-gray-600" for="date">Date</label>
                    <input id="date" type="date" name="date" value="{{ $report['date'] }}" class="border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm text-gray-600" for="clinic_id">Clinic</label>
                    <select id="clinic_id" name="clinic_id" class="border-gray-300 rounded-md shadow-sm">
                        <option value="">All clinics</option>
                        @foreach ($clinics as $clinic)
                            <option value="{{ $clinic->id }}" @selected($report['clinic_id'] === $clinic->id)>{{ $clinic->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-600" for="basis">Count by</label>
                    <select id="basis" name="basis" class="border-gray-300 rounded-md shadow-sm">
                        <option value="scheduled" @selected($report['basis'] === 'scheduled')>Appointment date</option>
                        <option value="created" @selected($report['basis'] === 'created')>Date booked</option>
                    </select>
                </div>
                <x-primary-button>Show</x-primary-button>
                <a href="{{ route('admin.reports.daily.export', request()->query()) }}" class="text-sm text-indigo-600 underline pb-2">Download CSV</a>
            </form>

            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                @foreach (['total' => 'Total', 'Scheduled' => 'Scheduled', 'Completed' => 'Completed', 'No-Show' => 'No-show', 'Cancelled' => 'Cancelled'] as $key => $label)
                    <div class="bg-white shadow-sm sm:rounded-lg p-4">
                        <div class="text-2xl font-semibold">{{ $report['totals'][$key] }}</div>
                        <div class="text-sm text-gray-600">{{ $label }}</div>
                    </div>
                @endforeach
            </div>

            <div class="overflow-x-auto bg-white shadow-sm sm:rounded-lg">
                <table class="min-w-full text-sm">
                    <caption class="text-left p-3 font-semibold">By clinic</caption>
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr><th class="p-3">Clinic</th><th class="p-3">Total</th><th class="p-3">Scheduled</th><th class="p-3">Completed</th><th class="p-3">No-show</th><th class="p-3">Cancelled</th></tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($report['clinics'] as $row)
                            <tr><td class="p-3">{{ $row['name'] }}</td><td class="p-3">{{ $row['total'] }}</td><td class="p-3">{{ $row['Scheduled'] }}</td><td class="p-3">{{ $row['Completed'] }}</td><td class="p-3">{{ $row['No-Show'] }}</td><td class="p-3">{{ $row['Cancelled'] }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="overflow-x-auto bg-white shadow-sm sm:rounded-lg">
                <table class="min-w-full text-sm">
                    <caption class="text-left p-3 font-semibold">By doctor</caption>
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr><th class="p-3">Doctor</th><th class="p-3">Clinic</th><th class="p-3">Total</th><th class="p-3">Scheduled</th><th class="p-3">Completed</th><th class="p-3">No-show</th><th class="p-3">Cancelled</th></tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($report['doctors'] as $row)
                            <tr><td class="p-3">{{ $row['name'] }}</td><td class="p-3">{{ $row['clinic'] }}</td><td class="p-3">{{ $row['total'] }}</td><td class="p-3">{{ $row['Scheduled'] }}</td><td class="p-3">{{ $row['Completed'] }}</td><td class="p-3">{{ $row['No-Show'] }}</td><td class="p-3">{{ $row['Cancelled'] }}</td></tr>
                        @empty
                            <tr><td colspan="7" class="p-6 text-center text-gray-500">No bookings for this day.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="overflow-x-auto bg-white shadow-sm sm:rounded-lg">
                <table class="min-w-full text-sm">
                    <caption class="text-left p-3 font-semibold">Appointments</caption>
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr><th class="p-3">Time</th><th class="p-3">Clinic</th><th class="p-3">Doctor</th><th class="p-3">Patient</th><th class="p-3">Status</th><th class="p-3"></th></tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($report['appointments'] as $a)
                            <tr>
                                <td class="p-3 whitespace-nowrap">{{ $a->scheduled_at->format('h:i A') }}</td>
                                <td class="p-3">{{ $a->clinic->name }}</td>
                                <td class="p-3">{{ $a->doctor->user->name }}</td>
                                <td class="p-3">{{ $a->patient->user->name }}</td>
                                <td class="p-3">{{ $a->status }}</td>
                                <td class="p-3 text-right">
                                    @if ($a->status !== 'Cancelled')
                                        <a href="{{ route('appointments.pdf', $a) }}" class="text-indigo-600">PDF</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-6 text-center text-gray-500">No appointments.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
