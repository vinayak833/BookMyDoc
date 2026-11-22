<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Doctors</h2>
            <a href="{{ route('admin.doctors.create') }}" class="text-sm bg-gray-800 text-white px-3 py-2 rounded-md">Add doctor</a>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @include('partials.flash')
            <div class="overflow-x-auto bg-white shadow-sm sm:rounded-lg">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr><th class="p-3">Name</th><th class="p-3">Email</th><th class="p-3">Clinic</th><th class="p-3">Specialisation</th><th class="p-3">Fee</th><th class="p-3"></th></tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($doctors as $doctor)
                            <tr>
                                <td class="p-3 font-medium">{{ $doctor->user->name }}</td>
                                <td class="p-3">{{ $doctor->user->email }}</td>
                                <td class="p-3">{{ $doctor->clinic->name }}</td>
                                <td class="p-3">{{ $doctor->specialisation }}</td>
                                <td class="p-3">₹{{ number_format($doctor->consultation_fee, 0) }}</td>
                                <td class="p-3 whitespace-nowrap text-right">
                                    <a href="{{ route('admin.doctors.edit', $doctor) }}" class="text-indigo-600 mr-3">Edit</a>
                                    <form method="POST" action="{{ route('admin.doctors.destroy', $doctor) }}" class="inline" onsubmit="return confirm('Delete this doctor?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-6 text-center text-gray-500">No doctors yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
