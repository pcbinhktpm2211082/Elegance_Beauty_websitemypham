<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if ($errors->has('oauth'))
        <div class="mb-4 text-sm text-red-600">
            {{ $errors->first('oauth') }}
        </div>
    @endif

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
                <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ml-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6">
        <a href="{{ route('google.redirect') }}"
           class="w-full inline-flex justify-center items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="h-5 w-5 mr-2" viewBox="0 0 533.5 544.3" aria-hidden="true">
                <path fill="#4285f4" d="M533.5 278.4c0-18.6-1.5-37-4.7-54.8H272v103.7h146.9c-6.3 34.5-25.2 63.7-54 83.4v68h87.1c51 46.9 81 115.9 81 192.1z"/>
                <path fill="#34a853" d="M272 544.3c73.5 0 135.3-24.3 180.4-66.2l-87.1-68c-24.3 16.2-55.4 25.7-93.3 25.7-71.8 0-132.6-48.5-154.3-113.7H28.3v71.2C73.8 480.7 165.3 544.3 272 544.3z"/>
                <path fill="#fbbc04" d="M117.7 322.1c-10.3-30.5-10.3-63.4 0-93.9V157H28.3c-41.6 81.8-41.6 179.5 0 261.3z"/>
                <path fill="#ea4335" d="M272 107.7c38.8-.6 76.1 13.6 104.4 39.6l78-78C407 24.5 341.3.6 272 0 165.3 0 73.8 63.6 28.3 160.7l89.4 71.2C139.4 156.2 200.2 107.7 272 107.7z"/>
            </svg>
            {{ __('Đăng nhập với Google') }}
        </a>
    </div>
</x-guest-layout>
