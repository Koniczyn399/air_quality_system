<x-modal name="add-measurement-modal" title="Dodaj ręczny pomiar">
    @if($device)
        <form wire:submit.prevent="save" class="space-y-4">
            <div>
                <x-input label="Data pomiaru" type="datetime-local" wire:model="measurementDate" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                @foreach($device->parameter_ids as $paramId)
                    <x-input
                        label="{{ \App\Models\Parameter::find($paramId)->name ?? 'Parametr '.$paramId }}"
                        wire:model="values.{{ $paramId }}"
                        type="number"
                        step="any"
                    />
                @endforeach
            </div>

            <div class="flex justify-end space-x-2">
                <x-button flat label="Anuluj" x-on:click="$dispatch('close-modal', 'add-measurement-modal')" />
                <x-button primary label="Zapisz pomiar" type="submit" />
            </div>
        </form>
    @else
        <p class="text-gray-500 text-center">Brak danych urządzenia.</p>
    @endif
</x-modal>
