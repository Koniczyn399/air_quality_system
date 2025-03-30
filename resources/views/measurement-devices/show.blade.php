<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Szczegóły urządzenia: {{ $measurementDevice->name }}</h1>
        
        <div class="bg-white shadow rounded-lg p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Podstawowe informacje -->
                <div>
                    <h2 class="text-lg font-medium mb-4">Informacje podstawowe</h2>
                    <div class="space-y-3">
                        <p><span class="font-medium">Nazwa:</span> {{ $measurementDevice->name }}</p>
                        <p><span class="font-medium">Model:</span> {{ $measurementDevice->model }}</p>
                        <p><span class="font-medium">Numer seryjny:</span> {{ $measurementDevice->serial_number }}</p>
                        <p><span class="font-medium">Status:</span> 
                            @switch($measurementDevice->status)
                                @case('active') <x-wireui-icon name="check-circle" class="w-5 h-5 text-green-500 inline" /> Aktywny @break
                                @case('inactive') <x-wireui-icon name="x-circle" class="w-5 h-5 text-red-500 inline" /> Nieaktywny @break
                                @case('in_repair') <x-wireui-icon name="key" class="w-5 h-5 text-yellow-500 inline" /> W naprawie @break
                            @endswitch
                        </p>
                    </div>
                </div>

                <!-- Daty kalibracji -->
                <div>
                    <h2 class="text-lg font-medium mb-4">Kalibracja</h2>
                    <div class="space-y-3">
                        <p><span class="font-medium">Ostatnia kalibracja:</span> {{ $measurementDevice->calibration_date->format('d-m-Y') }}</p>
                        <p><span class="font-medium">Następna kalibracja:</span> {{ $measurementDevice->next_calibration_date->format('d-m-Y') }}</p>
                        <p><span class="font-medium">Opis:</span> {{ $measurementDevice->description ?? 'Brak' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historia statusów - posortowana od najnowszych -->
<div class="bg-white shadow rounded-lg p-6 mt-6">
    <h2 class="text-lg font-medium mb-4">Historia zmian statusu</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Zmienił</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notatki</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($measurementDevice->statusHistory()->latest()->get() as $history)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $history->created_at->format('d-m-Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @switch($history->status)
                                @case('active') <x-wireui-icon name="check-circle" class="w-5 h-5 text-green-500 inline" /> Aktywny @break
                                @case('inactive') <x-wireui-icon name="x-circle" class="w-5 h-5 text-red-500 inline" /> Nieaktywny @break
                                @case('in_repair') <x-wireui-icon name="key" class="w-5 h-5 text-yellow-500 inline" /> W naprawie @break
                            @endswitch
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $history->changedBy->name ?? 'System' }}</td>
                        <td class="px-6 py-4">{{ $history->notes ?? 'Brak' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Brak historii zmian</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

        <div class="mt-6">
            <a href="{{ route('measurement-devices.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                Powrót do listy
            </a>
        </div>
    </div>
</x-app-layout>