<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl theme-text leading-tight">
            @if(isset($device_id))
                {{ __('Pomiary urządzenia') }} #{{ $device_id }}
            @else
                {{ __('Wszystkie pomiary') }}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="theme-bg theme-border border-b overflow-hidden shadow-xl sm:rounded-lg p-6">
                <livewire:values-table :device_id="$device_id ?? null" />
            </div>
        </div>
    </div>
</x-app-layout>