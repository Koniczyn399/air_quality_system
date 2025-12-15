<div class="max-w-3xl mx-auto py-6 sm:px-6 lg:px-8">
    
    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 transition-colors duration-200">
        
        <form wire:submit.prevent="submit">
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Data pomiaru
                </label>
                <input 
                    type="datetime-local" 
                    wire:model="measurements_date"
                    required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                />
                @error('measurements_date') <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
            </div>

            @if(!$isEditing)
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Wybierz urządzenie
                    </label>
                    <select 
                        wire:model.live="device_id" 
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                    >
                        <option value="">-- Wybierz urządzenie --</option>
                        @foreach($availableDevices as $device)
                            <option value="{{ $device->id }}">{{ $device->name }}</option>
                        @endforeach
                    </select>
                    @error('device_id') <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>
            @endif

            @if($parameters->isNotEmpty())
                <div class="grid md:grid-cols-2 gap-4 mt-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                    <h3 class="col-span-full text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">
                        Wartości pomiarowe
                    </h3>
                    
                    @foreach($parameters as $param)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ $param->name }} 
                                @if($param->unit) 
                                    <span class="text-xs text-gray-500 dark:text-gray-400">({{ $param->unit }})</span> 
                                @endif
                            </label>
                            <input 
                                type="number" 
                                step="any" 
                                wire:model="values.{{ $param->id }}" 
                                placeholder="Wpisz wartość"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring focus:ring-blue-500 focus:border-blue-500 shadow-sm"
                            />
                        </div>
                    @endforeach
                </div>

            @elseif($device_id)
                 <div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded text-yellow-800 dark:text-yellow-200">
                    Wybrane urządzenie nie posiada zdefiniowanych parametrów pomiarowych.
                </div>
            @endif

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('measurements.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                    Anuluj
                </a>

                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-blue-500 focus:bg-gray-700 dark:focus:bg-blue-700 active:bg-gray-900 dark:active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    {{ $isEditing ? 'Zapisz zmiany' : 'Dodaj pomiar' }}
                </button>
            </div>
        </form>
    </div>
</div>