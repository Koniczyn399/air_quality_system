@props(['value'])

<label {{ $attributes->merge(['class' => 'theme-label']) }}>
    {{ $value ?? $slot }}
</label>