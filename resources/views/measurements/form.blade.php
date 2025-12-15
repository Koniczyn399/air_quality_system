<x-app-layout>
    <div class="container mx-auto px-4 py-8 theme-container">
        @if (isset($measurement->id)) 
            <h1 class="text-2xl font-bold mb-6 theme-text">Edytuj pomiar</h1>
            <livewire:measurements.measurement-form :measurement="$measurement" />
        @else
            <h1 class="text-2xl font-bold mb-6 theme-text">Dodaj nowy pomiar</h1>
            <livewire:measurements.measurement-form 
                :measurement="$measurement" 
                :device-id="$deviceId ?? null" 
            />
        @endif
    </div>
</x-app-layout>