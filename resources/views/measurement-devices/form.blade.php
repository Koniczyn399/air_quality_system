<x-app-layout>
    <div class="container mx-auto px-4 py-8 theme-container">
        
                @if (isset($measurementDevice->id)) 
                    <h1 class="text-2xl font-bold mb-6 theme-text">Edytuj urządzenie: {{ $measurementDevice->name }}</h1>
                    <livewire:measurement-devices.measurement-device-form :measurementDevice="$measurementDevice"  />
                @elseif (isset($headers))
                    <h1 class="text-2xl font-bold mb-6 theme-text">Dodaj nowe urządzenie z wybranymi parametrami</h1>
                    <livewire:measurement-devices.measurement-device-form :headers="$headers"/>
                @else
                    <h1 class="text-2xl font-bold mb-6 theme-text">Dodaj nowe urządzenie</h1>
                    <livewire:measurement-devices.measurement-device-form />
                @endif


    </div>
</x-app-layout>