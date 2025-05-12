<x-app-layout>


<x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('translation.navigation.measurement_devices') }}
        </h2>
    </x-slot>



    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-2">
            <a href="{{ route('measurement-devices.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">
            {{__('translation.actions.add_new_device')}}
        </a>
        <a href="{{ route('data.upload') }}" class="bg-blue-500 text-white px-4 py-2 rounded">
        {{__('translation.actions.upload_data')}}
        </a>
            @can('create', App\Models\User::class)
                        <x-wireui-button primary label="{{ __('users.actions.create') }}"
                            href="{{ route('users.create') }}" class="justify-self-end" />
            @endcan
               
            @livewire('measurement-device-table')
            </div>
        </div>
    </div>


</x-app-layout>