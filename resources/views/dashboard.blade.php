<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl theme-text leading-tight">
            {{ __('Witaj') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="theme-bg overflow-hidden shadow-xl sm:rounded-lg">

            &nbsp;<b>Podsumowanie</b> <br>
            &nbsp;Urządzenia do kalibracji:
                <br>
                @foreach ($data as $device)
                    <hr class="my-2">
                    &nbsp;&nbsp;&nbsp; {{ $device->name }}: {{  $device->calibration_date }}  
                    <div class="" style="right: 30%;">
                    &nbsp;&nbsp;<x-wireui-button  href="{{ route('measurement-devices.show', ['measurement_device' => $device->id])}}" secondary class="mr-2"
                    label="{{ __('translation.placeholder.show') }}" 
                    />
                    </div>
                    <br>   
                @endforeach







                
            </div>
        </div>
    </div>
</x-app-layout>