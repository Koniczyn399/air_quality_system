<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl theme-text leading-tight">
        {{ __('translation.navigation.users') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="theme-bg theme-border border-b overflow-hidden shadow-xl sm:rounded-lg p-2">
                @can('create', App\Models\User::class)
                    <x-wireui-button primary label="{{ __('users.actions.create') }}" href="{{ route('users.create') }}" class="justify-self-end" />
                @endcan
               
                <livewire:users.user-table />
            </div>
        </div>
    </div>
</x-app-layout>