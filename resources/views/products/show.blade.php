<x-layouts.app>
    <div class="flex flex-col gap-12">
        <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
            <div class="bg-cream-200 aspect-video w-full overflow-hidden rounded-xl">
                <img
                    class="h-full w-full object-cover"
                    src="{{ $product->image }}"
                    alt="{{ $product->name }}"
                >
            </div>

            <div class="flex flex-col gap-4">
                <h1 class="text-2xl font-bold text-neutral-900">{{ $product->name }}</h1>
                <p class="text-neutral-600">{{ $product->description }}</p>
                <span
                    class="text-2xl font-semibold text-neutral-900">${{ number_format((float) $product->price, 2) }}</span>

                <livewire:add-to-cart-button :product="$product" />
            </div>
        </div>

        @if ($similar->isNotEmpty())
            <section class="flex flex-col gap-4">
                <h2 class="text-xl font-semibold text-neutral-900">{{ __('You might also like') }}</h2>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($similar as $item)
                        <x-product-card :product="$item" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-layouts.app>
