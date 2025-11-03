<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl  leading-tight">
    {{ __('Dodaj ręczny pomiar') }} — {{ $device->name ?? 'Nieznane urządzenie' }}
</h2>

    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('measurements.store') }}">
                    @csrf
                    <input type="hidden" name="device_id" value="{{ $device->id }}">


                    <div class="mb-6">
                        <label class="block text-gray-200 mb-2">Data pomiaru</label>
                        <input type="datetime-local"
                               name="measurements_date"
                               required
                               class="w-full rounded-lg border-gray-600 bg-gray-900 text-gray-100 focus:ring focus:ring-blue-500 focus:border-blue-500" />
                    </div>

                    @if($parameters->isNotEmpty())
                        <div class="grid md:grid-cols-2 gap-4">
                            @foreach($parameters as $param)
                                <div>
                                    <label class="block text-gray-200 mb-1">
                                        {{ $param->name }}
                                        @if($param->unit)
                                            <span class="text-gray-400 text-sm">({{ $param->unit }})</span>
                                        @endif
                                    </label>
                                    <input type="number"
                                           step="any"
                                           name="values[{{ $param->id }}]"
                                           placeholder="Wpisz wartość"
                                           class="w-full rounded-lg border-gray-600 bg-gray-900 text-gray-100 focus:ring focus:ring-blue-500 focus:border-blue-500" />
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-400 text-center mt-4">
                            To urządzenie nie ma przypisanych parametrów pomiarowych.
                        </p>
                    @endif

                    <div class="mt-8 flex justify-end gap-3">
                        <button
                            type="button"
                            onclick="window.location='{{ route('values.index', ['device_id' => $device->id]) }}'"
                            class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition"
                        >
                            Anuluj
                        </button>
                        <x-button primary type="submit">
                            Zapisz pomiar
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
