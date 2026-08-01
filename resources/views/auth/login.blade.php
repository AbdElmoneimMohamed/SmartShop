<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('Log in to your account')"
            :description="__('Enter your email and password below to log in')"
        />

        <x-auth-session-status
            class="text-center"
            :status="session('status')"
        />

        <form
            class="flex flex-col gap-6"
            method="POST"
            action="{{ route('login') }}"
        >
            @csrf

            <div class="flex flex-col gap-2">
                <label
                    class="text-sm font-medium text-neutral-700 dark:text-neutral-300"
                    for="email"
                >{{ __('Email address') }}</label>
                <input
                    class="focus:outline-hidden w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm text-neutral-900 focus:border-neutral-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white"
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="email@example.com"
                >
                @error('email')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex flex-col gap-2">
                <label
                    class="text-sm font-medium text-neutral-700 dark:text-neutral-300"
                    for="password"
                >{{ __('Password') }}</label>
                <input
                    class="focus:outline-hidden w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm text-neutral-900 focus:border-neutral-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white"
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="{{ __('Password') }}"
                >
                @error('password')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-neutral-600 dark:text-neutral-400">
                <input
                    class="rounded border-neutral-300 dark:border-neutral-700"
                    name="remember"
                    type="checkbox"
                >
                {{ __('Remember me') }}
            </label>

            <button
                class="w-full rounded-lg bg-neutral-900 px-4 py-2 text-sm font-semibold text-white hover:bg-neutral-700 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200"
                data-test="login-button"
                type="submit"
            >
                {{ __('Log in') }}
            </button>
        </form>

        @if (Route::has('register'))
            <div class="text-center text-sm text-neutral-600 dark:text-neutral-400">
                <span>{{ __('Don\'t have an account?') }}</span>
                <a
                    class="font-medium text-neutral-900 underline dark:text-white"
                    href="{{ route('register') }}"
                >{{ __('Sign up') }}</a>
            </div>
        @endif
    </div>
</x-layouts.auth>
