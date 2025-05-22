<button {{ $attributes->merge(['type' => 'button', 'class' => 'theme-button-danger']) }}>
    {{ $slot }}
</button>