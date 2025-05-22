<x-app-layout>
    <h1 class="text-2xl font-bold mb-6 theme-text">Urządzenia pomiarowe</h1>

    <div class="mb-4">
        <a href="{{ route('measurement-devices.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
            Dodaj nowe urządzenie
        </a>
    </div>

    @livewire('measurement-device-table')
</x-app-layout>