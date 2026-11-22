@csrf
<div class="space-y-4">
    <div>
        <x-input-label for="name" value="Clinic name" />
        <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name', $clinic->name ?? '')" required />
    </div>
    <div>
        <x-input-label for="address" value="Address" />
        <textarea id="address" name="address" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>{{ old('address', $clinic->address ?? '') }}</textarea>
    </div>
    <div>
        <x-input-label for="phone" value="Phone" />
        <x-text-input id="phone" name="phone" class="mt-1 block w-full" :value="old('phone', $clinic->phone ?? '')" />
    </div>
    <label class="inline-flex items-center gap-2">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300" @checked(old('is_active', $clinic->is_active ?? true))>
        <span class="text-sm text-gray-700">Active (visible to patients)</span>
    </label>
    <div class="flex items-center gap-3">
        <x-primary-button>Save</x-primary-button>
        <a href="{{ route('admin.clinics.index') }}" class="text-sm text-gray-600 underline">Cancel</a>
    </div>
</div>
