@props(['title' => '', 'description' => ''])

<div class="md:col-span-1 flex justify-between">
    <div class="px-4 sm:px-0">
        <h3 class="text-lg font-medium theme-text">{{ $title }}</h3>
        <p class="mt-1 text-sm theme-text-subtle">
            {{ $description }}
        </p>
    </div>

    <div class="px-4 sm:px-0">
        {{ $aside ?? '' }}
    </div>
</div>