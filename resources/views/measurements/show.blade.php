<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{__('translation.navigation.measurements')}}
        </h2>
    </x-slot>

  
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-2">


            Numer urządzenia: {{ $measurement->device_id }} <br>
            Data pomiaru: {{ $measurement->measurement_date }}
            <br>


            @foreach ($values as $value )
            {{ $value->parameter_name }}: {{$value->value }} {{ $value->unit_name }}

            <br>

            @endforeach


            </div>
        </div>
    </div>
</x-app-layout>
