<div>
    <button
        class="w-full rounded-lg bg-neutral-900 px-4 py-2 text-sm font-semibold text-white hover:bg-neutral-700 sm:w-auto"
        type="button"
        wire:click="addToCart"
    >
        {{ __('Add to cart') }}
    </button>

    @if ($addedToCart)
        <p class="mt-2 text-sm text-green-600">
            {{ __('Added to cart!') }}
            <a
                class="underline"
                href="{{ route('cart') }}"
                wire:navigate
            >{{ __('View cart') }}</a>
        </p>
    @endif
</div>
