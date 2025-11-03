<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Edytuj pomiar — {{ $device->name }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('measurements.update', $measurement->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <x-input label="Data pomiaru" type="datetime-local" name="measurements_date"
                                 value="{{ \Carbon\Carbon::parse($measurement->measurements_date)->format('Y-m-d\TH:i') }}" required />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        @foreach($parameters as $param)
                            @php
                                $value = $measurement->values->firstWhere('parameter_id', $param->id)->value ?? '';
                            @endphp
                            <x-input
                                label="{{ $param->name }} ({{ $param->unit ?? '' }})"
                                name="values[{{ $param->id }}]"
                                type="number"
                                step="any"
                                value="{{ $value }}"
                            />
                        @endforeach
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <x-button secondary type="button" x-on:click="window.location='{{ route('values.index', ['device_id' => $device->id]) }}'">
                            Anuluj
                        </x-button>
                        <x-button primary type="submit">
                            Zaktualizuj
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
