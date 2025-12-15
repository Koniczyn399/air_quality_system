<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Szczegóły pomiaru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 text-gray-900 dark:text-gray-100 transition-colors duration-200">

                <div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
                    <h3 class="text-2xl font-bold mb-2">
                        Urządzenie: 
                        <span class="text-blue-600 dark:text-blue-400">
                            {{ $measurement->device->name ?? 'Nieznane urządzenie' }}
                        </span>
                    </h3>

                    <p class="text-gray-600 dark:text-gray-300">
                        <span class="font-semibold">Data pomiaru:</span>
                        {{ $measurement->measurements_date }}
                    </p>
                </div>

                <h4 class="text-xl font-semibold mb-4">Wyniki pomiarów:</h4>

                <div class="grid md:grid-cols-2 gap-4">
                    @foreach ($values as $value)
                        <div class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <p class="text-gray-500 dark:text-gray-400 text-sm">
                                {{ $value->parameter_name }}
                            </p>
                            <p class="text-xl font-bold text-gray-800 dark:text-gray-100">
                                {{ $value->value }}
                                <span class="text-gray-500 dark:text-gray-400 text-sm">{{ $value->unit_name }}</span>
                            </p>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 flex justify-end">
                    <a href="{{ route('measurements.index') }}"
                       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        Powrót
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>