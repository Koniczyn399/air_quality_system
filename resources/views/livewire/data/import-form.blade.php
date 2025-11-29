<form wire:submit.prevent="submit">
    <h3 class="text-xl font-semibold leading-tight theme-text mb-2">
        {{ __('data.labels.data_management') }}
    </h3>


    <hr class="my-2 border-gray-200 dark:border-gray-700">

    <div class="grid grid-cols-2 gap-2">
        <div>
            <label for="file" class="block text-sm font-medium theme-text-subtle">
                {{ __('data.labels.select_file') }}
            </label>
        </div>
        <div>
            <x-wireui-input type="file" wire:model.defer="file" class="theme-input w-full" />
            @error('file') 
                <span class="text-red-600 dark:text-red-400">{{ $message }}</span> 
            @enderror

        </div>
    </div>

    @if ($eligible_devices)
    <hr class="my-2 border-gray-200 dark:border-gray-700">
    <div class="grid grid-cols-2 gap-2">
        <br><br><br>
        <div style="position: absolute; width:400px ">
        
           
            <x-wireui-select
                label="{{ __('data.attributes.select_devices') }}"
                placeholder="{{ __('data.actions.choose_device') }}"
                
                :options="$this->eligible_devices"
                wire:model.live="device_ids"
                option-label="name"
                option-value="id"
                class="w-full theme-input mt-2"
                
            />
        </div>
    </div>
    @endif

    <hr class="my-2 border-gray-200 dark:border-gray-700">

    <div class="flex justify-end pt-2">
        <x-wireui-button 
            href="{{ route('measurement-devices.index') }}" 
            secondary 
            label="{{ __('translation.placeholder.cancel') }}" 
            class="mr-2"
        />
        <x-wireui-button 
            type="submit" 
            primary 
            label="{{ __('translation.placeholder.save') }}" 
            spinner
        />
    </div>

    @if ($file)
        <hr class="my-2 border-gray-200 dark:border-gray-700">
        <div class="grid grid-cols-2 gap-2 mt-4">
            <dd class="mt-1 sm:mt-0 sm:col-span-2 text-sm theme-text">
                {{ print($meaurements) }}
            </dd>
        </div>
    @endif

</form>

