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
                    <x-wireui-button 
                        primary 
                        label="{{ __('translation.actions.add_new_device') }}" 
                        href="{{ route('measurement-devices.create') }}" 
                    />
                    <x-wireui-button 
                        primary 
                        label="{{ __('translation.actions.upload_data') }}" 
                        href="{{ route('data.upload') }}" 
                    />
                    <form method="GET" action="{{ route('measurement-devices.index') }}">
                        <input type="hidden" name="filter" value="{{ request('filter') === 'mine' ? 'all' : 'mine' }}">
                        <x-wireui-button type="submit">
                            {{ request('filter') === 'mine' ? 'Pokaż wszystkie urządzenia' : 'Pokaż moje urządzenia' }}
                        </x-wireui-button>
                    </form>
                </div>

                <!-- Tabela urządzeń -->
                @livewire('measurement-device-table', ['filter' => request('filter')])
            </div>
        </div>
    </div>
</x-app-layout>