<form wire:submit.prevent="submit">
    <h3 class="text-xl font-semibold leading-tight theme-text mb-2">
        {{ __('translation.attributes.actions') }}
    </h3>

    <hr class="my-2 border-gray-200 dark:border-gray-700">

    <div class="grid grid-cols-2 gap-2">
        <div>
            <label for="file" class="block text-sm font-medium theme-text-subtle">
                {{ __('translation.labels.export_file') }}
            </label>
        </div>
        <div>
            <x-wireui-button 
                a 
                href="{{ route('data.export') }}" 
                primary 
                label="{{ __('translation.placeholder.save') }}" 
                class="w-full"
            />
        </div>
    </div>

    <hr class="my-2 border-gray-200 dark:border-gray-700">

    <div class="grid grid-cols-2 gap-2">
        <div>
            <label for="file" class="block text-sm font-medium theme-text-subtle">
                {{ __('translation.labels.upload_file') }}
            </label>
        </div>
        <div>
            <input type="file" wire:model="file" class="theme-input w-full">
            @error('file') <span class="text-red-600 dark:text-red-400">{{ $message }}</span> @enderror

            <x-wireui-button 
                type="submit" 
                primary 
                label="{{ __('translation.placeholder.save') }}" 
                spinner 
                class="mt-2 w-full"
            />
        </div>
    </div>

    <hr class="my-2 border-gray-200 dark:border-gray-700">

    <div class="flex justify-end pt-2">
        <x-wireui-button 
            href="{{ route('measurement-devices.index') }}" 
            secondary 
            label="{{ __('translation.placeholder.cancel') }}" 
            class="mr-2"
        />
    </div>
</form>

@if ($file)
    <hr class="my-2 border-gray-200 dark:border-gray-700">
    <div class="grid grid-cols-2 gap-2 mt-4">
        {{ print($meaurements) }}
    </div>
@endif