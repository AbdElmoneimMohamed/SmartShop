<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-neutral-50 antialiased dark:bg-neutral-950">
    <nav class="border-b border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
            <a
                class="flex items-center gap-2 font-semibold text-neutral-900 dark:text-white"
                href="{{ route('home') }}"
                wire:navigate
            >
                <x-app-logo-icon class="size-7 fill-current text-black dark:text-white" />
                {{ config('app.name', 'SmartShop') }}
            </a>

            <div class="flex items-center gap-4 text-sm">
                <a
                    class="font-medium text-neutral-600 hover:text-neutral-900 dark:text-neutral-300 dark:hover:text-white"
                    href="{{ route('home') }}"
                    wire:navigate
                >
                    {{ __('Home') }}
                </a>
                <a
                    class="font-medium text-neutral-600 hover:text-neutral-900 dark:text-neutral-300 dark:hover:text-white"
                    href="{{ route('cart') }}"
                    wire:navigate
                >
                    {{ __('Cart') }}
                    <livewire:cart-badge />
                </a>

                @auth
                    <span class="hidden text-neutral-500 sm:inline dark:text-neutral-400">{{ auth()->user()->email }}</span>
                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                    >
                        @csrf
                        <button
                            class="font-medium text-neutral-600 hover:text-neutral-900 dark:text-neutral-300 dark:hover:text-white"
                            type="submit"
                        >
                            {{ __('Log out') }}
                        </button>
                    </form>
                @else
                    <a
                        class="font-medium text-neutral-600 hover:text-neutral-900 dark:text-neutral-300 dark:hover:text-white"
                        href="{{ route('login') }}"
                        wire:navigate
                    >
                        {{ __('Log in') }}
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
        {{ $slot }}
    </main>
</body>

</html>
