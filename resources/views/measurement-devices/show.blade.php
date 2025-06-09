<x-app-layout>
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8 theme-container">
        {{-- Nagłówek strony --}}
        <div class="mb-10">
            {{-- Nazwa urządzenia --}}
            <h1 class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400 text-center mt-2">
                {{ $measurementDevice->name }}
            </h1>
            <p class="mt-2 text-sm theme-text-subtle text-center">
                Przeglądaj szczegółowe informacje i historię urządzenia.
            </p>
        </div>

        {{-- Karty obok siebie --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 w-full mb-8">
            {{-- Karta: Informacje podstawowe --}}
            <div class="theme-bg shadow-xl rounded-xl overflow-hidden">
                <div class="p-6 sm:p-8">
                    <div class="flex items-center mb-6">
                        <x-wireui-icon name="information-circle" class="w-8 h-8 text-indigo-500 mr-3" />
                        <h2 class="text-xl font-semibold theme-text">Informacje podstawowe</h2>
                    </div>
                    <dl class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-4">
                            <dt class="text-sm font-medium theme-text-subtle">Nazwa</dt>
                            <dd class="mt-1 sm:mt-0 sm:col-span-2 text-sm theme-text">{{ $measurementDevice->name }}</dd>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-4">
                            <dt class="text-sm font-medium theme-text-subtle">Model</dt>
                            <dd class="mt-1 sm:mt-0 sm:col-span-2 text-sm theme-text">{{ $measurementDevice->model }}</dd>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-4">
                            <dt class="text-sm font-medium theme-text-subtle">Numer seryjny</dt>
                            <dd class="mt-1 sm:mt-0 sm:col-span-2 text-sm theme-text">{{ $measurementDevice->serial_number }}</dd>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-4">
                            <dt class="text-sm font-medium theme-text-subtle">Serwisant</dt>
                            <dd class="mt-1 sm:mt-0 sm:col-span-2 text-sm theme-text">
                                @if($measurementDevice->user)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                        <x-wireui-icon name="user" class="w-4 h-4 mr-1.5" />
                                        {{ $measurementDevice->user->name }}
                                    </span>
                                @else
                                    <span class="theme-text-subtle italic">Brak przypisanego</span>
                                @endif
                            </dd>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-4 items-center">
                            <dt class="text-sm font-medium theme-text-subtle">Status</dt>
                            <dd class="mt-1 sm:mt-0 sm:col-span-2 text-sm">
                                @switch($measurementDevice->status)
                                    @case('active')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold leading-5 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                            <x-wireui-icon name="check-circle" class="w-5 h-5 mr-1.5" /> Aktywny
                                        </span>
                                        @break
                                    @case('inactive')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold leading-5 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                            <x-wireui-icon name="x-circle" class="w-5 h-5 mr-1.5" /> Nieaktywny
                                        </span>
                                        @break
                                    @case('in_repair')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold leading-5 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                                            <x-wireui-icon name="wrench-screwdriver" class="w-5 h-5 mr-1.5" /> W naprawie
                                        </span>
                                        @break
                                    @default
                                        <span class="theme-text-subtle">{{ $measurementDevice->status }}</span>
                                @endswitch
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Karta: Dane dodatkowe i lokalizacja --}}
            <div class="theme-bg shadow-xl rounded-xl overflow-hidden">
                <div class="p-6 sm:p-8">
                    <div class="flex items-center mb-6">
                        <x-wireui-icon name="calendar-days" class="w-8 h-8 text-indigo-500 mr-3" />
                        <h2 class="text-xl font-semibold theme-text">Dane dodatkowe i lokalizacja</h2>
                    </div>
                    <dl class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-4">
                            <dt class="text-sm font-medium theme-text-subtle">Opis</dt>
                            <dd class="mt-1 sm:mt-0 sm:col-span-2 text-sm theme-text prose prose-sm max-w-none">
                                {!! $measurementDevice->description
                                    ? nl2br(e($measurementDevice->description))
                                    : '<span class="theme-text-subtle italic">Brak opisu</span>' !!}
                            </dd>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-4">
                            <dt class="text-sm font-medium theme-text-subtle">Ostatnia kalibracja</dt>
                            <dd class="mt-1 sm:mt-0 sm:col-span-2 text-sm theme-text">
                                {{ $measurementDevice->calibration_date
                                    ? $measurementDevice->calibration_date->format('d-m-Y')
                                    : 'Brak danych' }}
                            </dd>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-4">
                            <dt class="text-sm font-medium theme-text-subtle">Następna kalibracja</dt>
                            <dd class="mt-1 sm:mt-0 sm:col-span-2 text-sm theme-text">
                                {{ $measurementDevice->next_calibration_date
                                    ? $measurementDevice->next_calibration_date->format('d-m-Y')
                                    : 'Brak danych' }}
                            </dd>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-4">
                            <dt class="text-sm font-medium theme-text-subtle">Lokalizacja</dt>
                            <dd class="mt-1 sm:mt-0 sm:col-span-2 text-sm theme-text">
                                @if($measurementDevice->latitude && $measurementDevice->longitude)
                                    <x-wireui-icon name="map-pin" class="w-4 h-4 inline text-gray-400 mr-1" />
                                    {{ $measurementDevice->latitude }}, {{ $measurementDevice->longitude }}
                                    <span class="theme-text-subtle ml-1">({{ $city ?? 'Nieznane miasto' }})</span>
                                @else
                                    <span class="theme-text-subtle italic">Brak danych lokalizacyjnych</span>
                                @endif
                            </dd>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-4">
                            <dt class="text-sm font-medium theme-text-subtle">Parametry</dt>
                            <dd class="mt-1 sm:mt-0 sm:col-span-2 text-sm theme-text">
                               
                                @foreach($parameters as $parameter)
                                    {{ $parameter->name}},
                                @endforeach
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Karta: Historia zmian statusu --}}
        <div class="mt-10 theme-bg shadow-xl rounded-xl overflow-hidden">
            <div class="p-6 sm:p-8">
                <div class="flex items-center mb-6">
                    <x-wireui-icon name="clipboard-document-list" class="w-8 h-8 text-indigo-500 mr-3" />
                    <h2 class="text-xl font-semibold theme-text">Historia zmian statusu</h2>
                </div>
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium theme-text-subtle uppercase tracking-wider">Data</th>
                                <th class="px-6 py-3 text-left text-xs font-medium theme-text-subtle uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium theme-text-subtle uppercase tracking-wider">Zmieniony przez</th>
                                <th class="px-6 py-3 text-left text-xs font-medium theme-text-subtle uppercase tracking-wider">Notatki</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($measurementDevice->statusHistory()->latest()->get() as $history)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm theme-text">{{ $history->created_at->format('d-m-Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @switch($history->status)
                                            @case('active')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                                    <x-wireui-icon name="check-circle" class="w-4 h-4 mr-1.5" /> Aktywny
                                                </span>
                                                @break
                                            @case('inactive')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                                    <x-wireui-icon name="x-circle" class="w-4 h-4 mr-1.5" /> Nieaktywny
                                                </span>
                                                @break
                                            @case('in_repair')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                                                    <x-wireui-icon name="wrench-screwdriver" class="w-4 h-4 mr-1.5" /> W naprawie
                                                </span>
                                                @break
                                            @default
                                                <span class="theme-text-subtle">{{ $history->status }}</span>
                                        @endswitch
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm theme-text">
                                        @if($history->changedBy)
                                            <span class="inline-flex items-center">
                                                <x-wireui-icon name="user-circle" class="w-4 h-4 mr-1.5 text-gray-400" />
                                                {{ $history->changedBy->name }}
                                            </span>
                                        @else
                                            <span class="theme-text-subtle italic">System</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm theme-text prose prose-sm max-w-xs">
                                        {!! $history->notes ? nl2br(e($history->notes)) : '<span class="theme-text-subtle italic">Brak</span>' !!}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-sm theme-text-subtle">
                                        <div class="flex flex-col items-center">
                                            <x-wireui-icon name="document-magnifying-glass" class="w-12 h-12 text-gray-300 mb-2" />
                                            Brak historii zmian dla tego urządzenia.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Przycisk powrotu --}}
        <div class="mt-10 text-center sm:text-left">
            <a href="{{ route('measurement-devices.index') }}"
               class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                <x-wireui-icon name="arrow-left" class="w-5 h-5 mr-2 -ml-1" />
                Powrót do listy
            </a>
        </div>
    </div>
</x-app-layout>