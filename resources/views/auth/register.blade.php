<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('Create an account')"
            :description="__('Enter your details below to create your account')"
        />

        <x-auth-session-status
            class="text-center"
            :status="session('status')"
        />

        <form
            class="flex flex-col gap-6"
            method="POST"
            action="{{ route('register') }}"
        >
            @csrf

            <div class="flex flex-col gap-2">
                <label
                    class="text-sm font-medium text-neutral-700 dark:text-neutral-300"
                    for="name"
                >{{ __('Name') }}</label>
                <input
                    class="focus:outline-hidden w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm text-neutral-900 focus:border-neutral-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white"
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="{{ __('Full name') }}"
                >
                @error('name')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

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
                    autocomplete="new-password"
                    placeholder="{{ __('Password') }}"
                >
                @error('password')
                    <span class="text-sm text-red-600">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex flex-col gap-2">
                <label
                    class="text-sm font-medium text-neutral-700 dark:text-neutral-300"
                    for="password_confirmation"
                >{{ __('Confirm password') }}</label>
                <input
                    class="focus:outline-hidden w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm text-neutral-900 focus:border-neutral-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white"
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="{{ __('Confirm password') }}"
                >
            </div>

            <button
                class="w-full rounded-lg bg-neutral-900 px-4 py-2 text-sm font-semibold text-white hover:bg-neutral-700 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200"
                data-test="register-button"
                type="submit"
            >
                {{ __('Create account') }}
            </button>
        </form>

        <div class="text-center text-sm text-neutral-600 dark:text-neutral-400">
            <span>{{ __('Already have an account?') }}</span>
            <a
                class="font-medium text-neutral-900 underline dark:text-white"
                href="{{ route('login') }}"
            >{{ __('Log in') }}</a>
        </div>
    </div>
</x-layouts.auth>
