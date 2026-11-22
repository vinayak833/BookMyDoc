<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 border-t border-gray-200 pt-5">
        <h2 class="mb-3 text-sm font-semibold text-gray-700">Demo accounts</h2>
        <div class="grid grid-cols-1 gap-2">
            @foreach ([
                ['label' => 'Admin', 'email' => 'admin@bookmydoc.test'],
                ['label' => 'Doctor - Rao', 'email' => 'dr.rao@bookmydoc.test'],
                ['label' => 'Doctor - Nair', 'email' => 'dr.nair@bookmydoc.test'],
                ['label' => 'Patient', 'email' => 'patient@bookmydoc.test'],
            ] as $account)
                <button
                    type="button"
                    class="demo-account flex items-center justify-between rounded-md border border-gray-200 px-3 py-2 text-left text-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    data-email="{{ $account['email'] }}"
                >
                    <span class="font-medium text-gray-800">{{ $account['label'] }}</span>
                    <span class="text-xs text-gray-500">{{ $account['email'] }}</span>
                </button>
            @endforeach
        </div>
        <p class="mt-2 text-xs text-gray-500">Password for every demo account: <strong>password</strong></p>
    </div>

    <script>
        document.querySelectorAll('.demo-account').forEach((button) => {
            button.addEventListener('click', () => {
                document.getElementById('email').value = button.dataset.email;
                document.getElementById('password').value = 'password';
                document.getElementById('password').focus();
            });
        });
    </script>
</x-guest-layout>
