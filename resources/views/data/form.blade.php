<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl theme-text leading-tight">
        {{ __('data.labels.data_management') }}
        </h2>
    </x-slot>
    <div class="py-12 theme-container">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="theme-bg overflow-hidden shadow-xl sm:rounded-lg p-4">
            <livewire:data.data-form />
            </div>
        </div>
    </div>
</x-app-layout>
