<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl theme-text leading-tight">
            {{ __('translation.navigation.measurement_devices') }}
        </h2>
    </x-slot>

    <div class="py-12 theme-container">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="theme-bg overflow-hidden shadow-xl sm:rounded-lg p-4">
                <!-- Przyciski akcji -->
                <div class="flex flex-wrap gap-3 mb-4">
                    <a href="{{ route('measurement-devices.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md shadow-sm transition-colors duration-200">
                        {{ __('translation.actions.add_new_device') }}
                    </a>
                    <a href="{{ route('data.upload') }}" 
                       class="inline-flex items-center px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-md shadow-sm transition-colors duration-200">
                        {{ __('translation.actions.upload_data') }}
                    </a>
                </div>

                <!-- Tabela urządzeń -->
                @livewire('measurement-device-table')
            </div>
        </div>
    </div>
</x-app-layout>