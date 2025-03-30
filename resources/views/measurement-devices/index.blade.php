<x-app-layout>
    <h1 class="text-2xl font-bold mb-6">Urządzenia pomiarowe</h1>
    <div class="mb-4">
        <a href="{{ route('measurement-devices.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">
            Dodaj nowe urządzenie
        </a>
    </div>
    @livewire('measurement-device-table')
</x-app-layout>