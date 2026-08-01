<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="bg-cream-100 min-h-screen antialiased">
    <nav class="bg-cream-50 border-b border-neutral-200">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
            <a
                class="flex items-center gap-2 font-semibold text-neutral-900"
                href="{{ route('home') }}"
                wire:navigate
            >
                <x-app-logo-icon class="size-7 fill-current text-black" />
                {{ config('app.name', 'SmartShop') }}
            </a>

            <div class="flex items-center gap-4 text-sm">
                <a
                    class="font-medium text-neutral-600 hover:text-neutral-900"
                    href="{{ route('home') }}"
                    wire:navigate
                >
                    {{ __('Home') }}
                </a>
                <a
                    class="font-medium text-neutral-600 hover:text-neutral-900"
                    href="{{ route('cart') }}"
                    wire:navigate
                >
                    {{ __('Cart') }}
                    <livewire:cart-badge />
                </a>

                @auth
                    <span class="hidden text-neutral-500 sm:inline">{{ auth()->user()->email }}</span>
                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                    >
                        @csrf
                        <button
                            class="font-medium text-neutral-600 hover:text-neutral-900"
                            type="submit"
                        >
                            {{ __('Log out') }}
                        </button>
                    </form>
                @else
                    <a
                        class="font-medium text-neutral-600 hover:text-neutral-900"
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
