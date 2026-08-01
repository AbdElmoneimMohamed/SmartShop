@props(['product'])

<a
    class="bg-cream-50 group flex flex-col overflow-hidden rounded-xl border border-neutral-200 transition hover:shadow-md"
    href="{{ route('products.show', $product) }}"
    wire:navigate
>
    <div class="bg-cream-200 aspect-video w-full overflow-hidden">
        <img
            class="h-full w-full object-cover transition group-hover:scale-105"
            src="{{ $product->image }}"
            alt="{{ $product->name }}"
        >
    </div>
    <div class="flex flex-1 flex-col gap-1 p-4">
        <h3 class="font-medium text-neutral-900">{{ $product->name }}</h3>
        <p class="line-clamp-2 text-sm text-neutral-500">{{ $product->description }}</p>
        <span class="mt-auto pt-2 font-semibold text-neutral-900">${{ number_format((float) $product->price, 2) }}</span>
    </div>
</a>
