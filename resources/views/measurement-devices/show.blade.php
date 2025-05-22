<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6 theme-text">Szczegóły urządzenia: {{ $measurementDevice->name }}</h1>

        <div class="theme-bg shadow rounded-lg p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Podstawowe informacje -->
                <div>
                    <h2 class="text-lg font-medium mb-4 theme-text">Informacje podstawowe</h2>
                    <div class="space-y-3">
                        <p><span class="font-medium theme-text">Nazwa:<span class="theme-text-subtle"> {{ $measurementDevice->name }}</span></p>
                        <p><span class="font-medium theme-text">Model:<span class="theme-text-subtle"> {{ $measurementDevice->model }}</span></p>
                        <p><span class="font-medium theme-text">Numer seryjny:<span class="theme-text-subtle"> {{ $measurementDevice->serial_number }}</span></p>
                        <p><span class="font-medium theme-text">Status:<span class="theme-text-subtle">
                            @switch($measurementDevice->status)
                                @case('active') <x-wireui-icon name="check-circle" class="w-5 h-5 text-green-500 inline" /> Aktywny @break
                                @case('inactive') <x-wireui-icon name="x-circle" class="w-5 h-5 text-red-500 inline" /> Nieaktywny @break
                                @case('in_repair') <x-wireui-icon name="key" class="w-5 h-5 text-yellow-500 inline" /> W naprawie @break
                            @endswitch
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Daty kalibracji -->
                <div>
                    <h2 class="text-lg font-medium mb-4 theme-text">Kalibracja</h2>
                    <div class="space-y-3">
                        <p><span class="font-medium theme-text">Ostatnia kalibracja:<span class="theme-text-subtle"> {{ $measurementDevice->calibration_date->format('d-m-Y') }}</span></p>
                        <p><span class="font-medium theme-text">Następna kalibracja:<span class="theme-text-subtle"> {{ $measurementDevice->next_calibration_date->format('d-m-Y') }}</span></p>
                        <p><span class="font-medium theme-text">Opis:<span class="theme-text-subtle"> {{ $measurementDevice->description ?? 'Brak' }}</span></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historia statusów - posortowana od najnowszych -->
        <div class="theme-bg shadow rounded-lg p-6 mt-6">
            <h2 class="text-lg font-medium mb-4 theme-text">Historia zmian statusu</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-theme-border">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium theme-text uppercase">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-medium theme-text uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium theme-text uppercase">Zmienił</th>
                            <th class="px-6 py-3 text-left text-xs font-medium theme-text uppercase">Notatki</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-theme-border">
                        @forelse($measurementDevice->statusHistory()->latest()->get() as $history)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap theme-text-subtle">{{ $history->created_at->format('d-m-Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @switch($history->status)
                                        @case('active') <x-wireui-icon name="check-circle" class="w-5 h-5 text-green-500 inline" /> Aktywny @break
                                        @case('inactive') <x-wireui-icon name="x-circle" class="w-5 h-5 text-red-500 inline" /> Nieaktywny @break
                                        @case('in_repair') <x-wireui-icon name="key" class="w-5 h-5 text-yellow-500 inline" /> W naprawie @break
                                    @endswitch
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap theme-text-subtle">{{ $history->changedBy->name ?? 'System' }}</td>
                                <td class="px-6 py-4 theme-text-subtle">{{ $history->notes ?? 'Brak' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center theme-text-subtle">Brak historii zmian</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('measurement-devices.index') }}" class="px-4 py-2 border theme-border rounded-md text-sm font-medium theme-text hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 ease-in-out">
                Powrót do listy
            </a>
        </div>
    </div>
</x-app-layout>