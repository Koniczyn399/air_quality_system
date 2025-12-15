<div class="bg-gray-800 shadow sm:rounded-lg p-6">
    <form wire:submit.prevent="submit">
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-400 mb-1">Data pomiaru</label>
            <input 
                type="datetime-local" 
                wire:model="measurements_date"
                required
                class="w-full rounded-lg border-gray-600 bg-gray-900 text-gray-100 focus:ring focus:ring-blue-500 focus:border-blue-500"
            />
            @error('measurements_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        @if(!$isEditing)
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-400 mb-1">Wybierz urządzenie</label>
                <select 
                    wire:model.live="device_id" 
                    class="w-full rounded-lg border-gray-600 bg-gray-900 text-gray-100 focus:ring focus:ring-blue-500 focus:border-blue-500"
                >
                    <option value="">-- Wybierz urządzenie --</option>
                    @foreach($availableDevices as $device)
                        <option value="{{ $device->id }}">{{ $device->name }}</option>
                    @endforeach
                </select>
                @error('device_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        @endif

        @if($parameters->isNotEmpty())
            <div class="grid md:grid-cols-2 gap-4 mt-4 p-4 border border-gray-700 rounded-lg">
                <h3 class="col-span-full text-lg font-semibold text-gray-300 mb-2">Wartości pomiarowe</h3>
                
                @foreach($parameters as $param)
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">
                            {{ $param->name }} ({{ $param->unit ?? '-' }})
                        </label>
                        <input 
                            type="number" 
                            step="any" 
                            wire:model="values.{{ $param->id }}" 
                            placeholder="Wpisz wartość"
                            class="w-full rounded-lg border-gray-600 bg-gray-900 text-gray-100 focus:ring focus:ring-blue-500 focus:border-blue-500"
                        />
                    </div>
                @endforeach
            </div>
        @elseif($device_id)
             <div class="mt-4 p-4 bg-yellow-900/20 border border-yellow-700 rounded text-yellow-200">
                Wybrane urządzenie nie posiada zdefiniowanych parametrów pomiarowych.
            </div>
        @endif

        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ $device_id ? route('values.index', ['device_id' => $device_id]) : route('measurements.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                Anuluj
            </a>

            <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                {{ $isEditing ? 'Zapisz zmiany' : 'Dodaj pomiar' }}
            </button>
        </div>
    </form>
</div>