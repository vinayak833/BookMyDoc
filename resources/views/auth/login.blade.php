<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form id="login-form" method="POST" action="{{ route('login') }}">
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
        <p class="mb-3 text-sm font-semibold text-gray-700">Demo accounts</p>
        <p class="mb-3 text-xs text-gray-500">Password for all accounts: <span class="font-mono font-semibold text-gray-700">password</span></p>
        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
            <button type="button" class="demo-account rounded-lg border border-gray-200 px-3 py-2 text-left hover:border-blue-300 hover:bg-blue-50" data-email="admin@bookmydoc.test">
                <span class="block text-sm font-semibold text-gray-800">Admin</span>
                <span class="block text-xs text-gray-500">admin@bookmydoc.test</span>
            </button>
            <button type="button" class="demo-account rounded-lg border border-gray-200 px-3 py-2 text-left hover:border-blue-300 hover:bg-blue-50" data-email="patient@bookmydoc.test">
                <span class="block text-sm font-semibold text-gray-800">Patient</span>
                <span class="block text-xs text-gray-500">patient@bookmydoc.test</span>
            </button>
            <button type="button" class="demo-account rounded-lg border border-gray-200 px-3 py-2 text-left hover:border-blue-300 hover:bg-blue-50" data-email="dr.rao@bookmydoc.test">
                <span class="block text-sm font-semibold text-gray-800">Dr. Anita Rao</span>
                <span class="block text-xs text-gray-500">dr.rao@bookmydoc.test</span>
            </button>
            <button type="button" class="demo-account rounded-lg border border-gray-200 px-3 py-2 text-left hover:border-blue-300 hover:bg-blue-50" data-email="dr.nair@bookmydoc.test">
                <span class="block text-sm font-semibold text-gray-800">Dr. Suresh Nair</span>
                <span class="block text-xs text-gray-500">dr.nair@bookmydoc.test</span>
            </button>
        </div>
    </div>

    <script>
        document.querySelectorAll('.demo-account').forEach((button) => {
            button.addEventListener('click', () => {
                document.getElementById('email').value = button.dataset.email;
                document.getElementById('password').value = 'password';
                document.getElementById('login-form').submit();
            });
        });
    </script>
</x-guest-layout>
