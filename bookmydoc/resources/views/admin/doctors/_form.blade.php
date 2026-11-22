@csrf
<div class="space-y-4">
    <div>
        <x-input-label for="name" value="Doctor name" />
        <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name', $doctor->user->name ?? '')" required />
    </div>
    <div>
        <x-input-label for="email" value="Login email" />
        <x-text-input id="email" type="email" name="email" class="mt-1 block w-full" :value="old('email', $doctor->user->email ?? '')" required />
    </div>
    <div>
        <x-input-label for="password" :value="isset($doctor) ? 'New password (leave blank to keep)' : 'Password (min 8 characters)'" />
        <x-text-input id="password" type="password" name="password" class="mt-1 block w-full" :required="! isset($doctor)" />
    </div>
    <div>
        <x-input-label for="clinic_id" value="Clinic" />
        <select id="clinic_id" name="clinic_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
            @foreach ($clinics as $clinic)
                <option value="{{ $clinic->id }}" @selected(old('clinic_id', $doctor->clinic_id ?? '') == $clinic->id)>{{ $clinic->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <x-input-label for="reg_no" value="Registration no." />
            <x-text-input id="reg_no" name="reg_no" class="mt-1 block w-full" :value="old('reg_no', $doctor->reg_no ?? '')" required />
        </div>
        <div>
            <x-input-label for="specialisation" value="Specialisation" />
            <x-text-input id="specialisation" name="specialisation" class="mt-1 block w-full" :value="old('specialisation', $doctor->specialisation ?? '')" required />
        </div>
        <div>
            <x-input-label for="consultation_fee" value="Fee (INR)" />
            <x-text-input id="consultation_fee" type="number" step="0.01" min="0" name="consultation_fee" class="mt-1 block w-full" :value="old('consultation_fee', $doctor->consultation_fee ?? '')" required />
        </div>
    </div>
    <div class="flex items-center gap-3">
        <x-primary-button>Save</x-primary-button>
        <a href="{{ route('admin.doctors.index') }}" class="text-sm text-gray-600 underline">Cancel</a>
    </div>
</div>
