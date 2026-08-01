@props(['product'])

<a
    class="group flex flex-col overflow-hidden rounded-xl border border-neutral-200 bg-white transition hover:shadow-md dark:border-neutral-800 dark:bg-neutral-900"
    href="{{ route('products.show', $product) }}"
    wire:navigate
>
    <div class="aspect-video w-full overflow-hidden bg-neutral-100 dark:bg-neutral-800">
        <img
            class="h-full w-full object-cover transition group-hover:scale-105"
            src="{{ $product->image }}"
            alt="{{ $product->name }}"
        >
    </div>
    <div class="flex flex-1 flex-col gap-1 p-4">
        <h3 class="font-medium text-neutral-900 dark:text-white">{{ $product->name }}</h3>
        <p class="line-clamp-2 text-sm text-neutral-500 dark:text-neutral-400">{{ $product->description }}</p>
        <span
            class="mt-auto pt-2 font-semibold text-neutral-900 dark:text-white">${{ number_format((float) $product->price, 2) }}</span>
    </div>
</a>
