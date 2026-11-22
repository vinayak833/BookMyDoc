<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Clinics</h2>
            <a href="{{ route('admin.clinics.create') }}" class="text-sm bg-gray-800 text-white px-3 py-2 rounded-md">Add clinic</a>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @include('partials.flash')
            <div class="overflow-x-auto bg-white shadow-sm sm:rounded-lg">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr><th class="p-3">Name</th><th class="p-3">Address</th><th class="p-3">Doctors</th><th class="p-3">Status</th><th class="p-3"></th></tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($clinics as $clinic)
                            <tr>
                                <td class="p-3 font-medium">{{ $clinic->name }}</td>
                                <td class="p-3">{{ $clinic->address }}</td>
                                <td class="p-3">{{ $clinic->doctors_count }}</td>
                                <td class="p-3">{{ $clinic->is_active ? 'Active' : 'Inactive' }}</td>
                                <td class="p-3 whitespace-nowrap text-right">
                                    <a href="{{ route('admin.clinics.edit', $clinic) }}" class="text-indigo-600 mr-3">Edit</a>
                                    <form method="POST" action="{{ route('admin.clinics.destroy', $clinic) }}" class="inline" onsubmit="return confirm('Delete this clinic?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-6 text-center text-gray-500">No clinics yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
