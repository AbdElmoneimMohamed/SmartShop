<div class="flex flex-col gap-8">
    @if ($checkedOut)
        <div
            class="rounded-xl border border-green-200 bg-green-50 p-8 text-center dark:border-green-900 dark:bg-green-950">
            <h1 class="text-2xl font-bold text-green-700 dark:text-green-400">{{ __('Order confirmed!') }}</h1>
            <p class="mt-2 text-green-600 dark:text-green-500">
                {{ __('Thanks for your purchase. This was a simulated checkout — no payment was taken.') }}
            </p>
            <a
                class="mt-4 inline-block rounded-lg bg-neutral-900 px-4 py-2 text-sm font-semibold text-white dark:bg-white dark:text-neutral-900"
                href="{{ route('home') }}"
                wire:navigate
            >
                {{ __('Continue shopping') }}
            </a>
        </div>
    @else
        <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">{{ __('Your cart') }}</h1>

        @if ($checkoutFailed)
            <div
                class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-900 dark:bg-red-950 dark:text-red-400">
                {{ __('Payment could not be processed. Please try again.') }}
            </div>
        @endif

        @if ($items->isEmpty())
            <p class="text-neutral-500 dark:text-neutral-400">
                {{ __('Your cart is empty.') }}
                <a
                    class="underline"
                    href="{{ route('home') }}"
                    wire:navigate
                >{{ __('Browse products') }}</a>
            </p>
        @else
            <div class="flex flex-col divide-y divide-neutral-200 dark:divide-neutral-800">
                @foreach ($items as $item)
                    <div
                        class="flex items-center gap-4 py-4"
                        wire:key="cart-item-{{ $item->product->id }}"
                    >
                        <img
                            class="h-16 w-16 rounded-lg object-cover"
                            src="{{ $item->product->image }}"
                            alt="{{ $item->product->name }}"
                        >
                        <div class="flex-1">
                            <p class="font-medium text-neutral-900 dark:text-white">{{ $item->product->name }}</p>
                            <p class="text-sm text-neutral-500 dark:text-neutral-400">
                                ${{ number_format((float) $item->product->price, 2) }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                class="h-8 w-8 rounded-lg border border-neutral-300 text-neutral-600 hover:bg-neutral-100 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800"
                                type="button"
                                @click="$wire.decrement({{ $item->product->id }})"
                            >−</button>
                            <span class="w-8 text-center">{{ $item->quantity }}</span>
                            <button
                                class="h-8 w-8 rounded-lg border border-neutral-300 text-neutral-600 hover:bg-neutral-100 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800"
                                type="button"
                                @click="$wire.increment({{ $item->product->id }})"
                            >+</button>
                        </div>
                        <span
                            class="w-20 text-right font-semibold text-neutral-900 dark:text-white">${{ number_format($item->subtotal(), 2) }}</span>
                        <button
                            class="text-sm text-neutral-400 hover:text-red-600"
                            type="button"
                            wire:click="remove({{ $item->product->id }})"
                        >
                            {{ __('Remove') }}
                        </button>
                    </div>
                @endforeach
            </div>

            <div class="flex items-center justify-between border-t border-neutral-200 pt-4 dark:border-neutral-800">
                <span class="text-lg font-semibold text-neutral-900 dark:text-white">{{ __('Total') }}</span>
                <span
                    class="text-lg font-semibold text-neutral-900 dark:text-white">${{ number_format($total, 2) }}</span>
            </div>

            <button
                class="w-full rounded-lg bg-neutral-900 px-4 py-3 text-sm font-semibold text-white hover:bg-neutral-700 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200"
                type="button"
                wire:click="checkout"
            >
                {{ __('Checkout') }}
            </button>
        @endif
    @endif
</div>
