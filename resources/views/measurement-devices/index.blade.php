<x-app-layout>
    <h1 class="text-2xl font-bold mb-6">{{__('translation.navigation.measurement_devices')}}</h1>
    <div class="mb-4">
        <a href="{{ route('measurement-devices.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">
            {{__('translation.actions.add_new_device')}}
        </a>
        <a href="{{ route('data.upload') }}" class="bg-blue-500 text-white px-4 py-2 rounded">
        {{__('translation.actions.upload_data')}}
        </a>
    </div>
</x-app-layout>