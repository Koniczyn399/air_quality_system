@php
    use App\Models\MeasurementDevice;

    $device = null;
    if (isset($device_id) && $device_id) {
        $device = MeasurementDevice::find($device_id);
    }
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl theme-text leading-tight">
            {{ __('Pomiary urządzenia:') }} <span class="text-blue-600">{{ $device->name ?? 'Nieznane urządzenie' }}</span>
        </h2>
    </x-slot>

    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="theme-bg theme-border border-b overflow-hidden shadow-xl sm:rounded-lg p-6">
                @if($device)
                    

                        <x-wireui-button
                            primary
                            label="{{ __('Dodaj pomiar') }}"
                            href="{{ route('measurements.create', ['device' => $device->id]) }}"
                            class="justify-self-end"
                        />
                    
                @endif
                <livewire:values-table :device_id="$device_id ?? null" />

                <livewire:data.chart-form :device_id="$device_id ?? null">
            </div>
        </div>
    </div>
</x-app-layout>