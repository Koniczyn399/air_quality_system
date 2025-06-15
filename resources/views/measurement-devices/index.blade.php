<x-app-layout>
<<<<<<<<< Temporary merge branch 1
    <h1 class="text-2xl text-white font-bold mb-6">Urządzenia pomiarowe</h1>
=========
    <h1 class="text-2xl font-bold mb-6">{{__('translation.navigation.measurement_devices')}}</h1>
>>>>>>>>> Temporary merge branch 2
    <div class="mb-4">
        <a href="{{ route('measurement-devices.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">
            {{__('translation.actions.add_new_device')}}
        </a>
        <a href="{{ route('data.upload') }}" class="bg-blue-500 text-white px-4 py-2 rounded">
        {{__('translation.actions.upload_data')}}
        </a>
    </div>
</x-app-layout>