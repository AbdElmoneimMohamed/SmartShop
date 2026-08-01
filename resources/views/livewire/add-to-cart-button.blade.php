<div>
    <button
        class="w-full rounded-lg bg-neutral-900 px-4 py-2 text-sm font-semibold text-white hover:bg-neutral-700 sm:w-auto dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200"
        type="button"
        wire:click="addToCart"
    >
        {{ __('Add to cart') }}
    </button>

    @if ($addedToCart)
        <p class="mt-2 text-sm text-green-600 dark:text-green-400">
            {{ __('Added to cart!') }}
            <a
                class="underline"
                href="{{ route('cart') }}"
                wire:navigate
            >{{ __('View cart') }}</a>
        </p>
    @endif
</div>
