@props(['title', 'description'])

<div class="flex w-full flex-col gap-1 text-center">
    <h1 class="text-xl font-semibold text-neutral-900">{{ $title }}</h1>
    <p class="text-sm text-neutral-500">{{ $description }}</p>
</div>
