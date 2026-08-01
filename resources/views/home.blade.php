<x-layouts.app>
    <div
        class="flex flex-col gap-12"
        x-data="{ query: '' }"
    >
        <section class="flex flex-col items-center gap-4 rounded-2xl bg-neutral-900 px-6 py-16 text-center text-white">
            <h1 class="text-3xl font-bold sm:text-4xl">{{ __('Shop smarter, not harder') }}</h1>
            <p class="max-w-xl text-neutral-300">
                {{ __('Browse our curated catalog and let AI point you toward what you\'ll actually like.') }}
            </p>
        </section>

        <div class="mx-auto w-full max-w-md">
            <input
                class="focus:outline-hidden w-full rounded-lg border border-neutral-300 px-4 py-2 text-sm focus:border-neutral-500"
                type="search"
                x-model.debounce.150ms="query"
                placeholder="{{ __('Search products…') }}"
            >
        </div>

        @if ($recommended->isNotEmpty())
            <section
                class="flex flex-col gap-4"
                x-show="query.trim() === ''"
            >
                <h2 class="text-xl font-semibold text-neutral-900">{{ __('Recommended for you') }}</h2>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($recommended as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            </section>
        @endif

        <section class="flex flex-col gap-4">
            <h2 class="text-xl font-semibold text-neutral-900">{{ __('All products') }}</h2>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($products as $product)
                    <div
                        x-data="{ haystack: @js(mb_strtolower($product->name . ' ' . $product->description)) }"
                        x-show="query.trim() === '' || haystack.includes(query.trim().toLowerCase())"
                    >
                        <x-product-card :product="$product" />
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</x-layouts.app>
