<button {{ $attributes->merge(['type' => 'button', 'class' => 'theme-button-secondary']) }}>
    {{ $slot }}
</button>