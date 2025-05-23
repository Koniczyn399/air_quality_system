<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl theme-text leading-tight">
            {{ __('translation.navigation.measurements') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="theme-bg theme-border border-b overflow-hidden shadow-xl sm:rounded-lg p-2">
                <x-wireui-button primary label="{{ __('translation.actions.upload_data') }}" href="{{ route('data.upload') }}" class="justify-self-end" />

                <livewire:measurements.measurement-table />
            </div>
        </div>
    </div>
</x-app-layout>