<div class="container mx-auto px-4 py-8">
    <form wire:submit.prevent="submit">

        <div class="grid grid-cols-1 gap-6 mb-8">

            <!-- Data pomiaru -->
            <div>
                <x-wireui-datetime-picker
                    name="measurements_date"
                    label="Data pomiaru"
                    required
                    wire:model="measurements_date"
                />
                @error('measurements_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Parametry pomiaru -->
            @if(count($parameters) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($parameters as $param)
                        <div class="col-span-1">
                            <x-wireui-input
                                label="{{ $param->name }} ({{ $param->unit ?? '' }})"
                                name="values.{{ $param->id }}"
                                type="number"
                                step="any"
                                wire:model.defer="values.{{ $param->id }}"
                            />
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-md p-4">
                    <p class="text-yellow-700 dark:text-yellow-300">Brak parametrów przypisanych do tego urządzenia.</p>
                </div>
            @endif

        </div>

        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('values.index', ['device_id' => $device_id]) }}" 
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium theme-text hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                Anuluj
            </a>

            @if($isEditing)
                <x-wireui-button type="submit" primary label="Zaktualizuj pomiar" />
            @else
                <x-wireui-button type="submit" primary label="Dodaj pomiar" />
            @endif
        </div>
    </form>
</div>