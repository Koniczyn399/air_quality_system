<form wire:submit.prevent="submit">
    <h3 class="text-xl font-semibold leading-tight theme-text mb-2">
        {{ __('data.labels.data_management') }}
    </h3>

    <hr class="my-2 border-gray-200 dark:border-gray-700">

    <div class="grid grid-cols-2 gap-2">
        <div>
            <label for="start_date" class="block text-sm font-medium theme-text-subtle">
                {{ __('data.attributes.start_date') }}
            </label>
        </div>
        <div>
            <x-wireui-datetime-picker
                wire:model.live="start_date"
                placeholder="{{ __('data.attributes.start_date') }}"
                parse-format="YYYY-MM-DD"
                class="w-full theme-input"
            />
        </div>
    </div>

    <hr class="my-2 border-gray-200 dark:border-gray-700">

    <div class="grid grid-cols-2 gap-2">
        <div>
            <label for="end_date" class="block text-sm font-medium theme-text-subtle">
                {{ __('data.attributes.end_date') }}
            </label>
        </div>
        <div>
            <x-wireui-datetime-picker
                wire:model.live="end_date"
                placeholder="{{ __('data.attributes.end_date') }}"
                parse-format="YYYY-MM-DD"
                class="w-full theme-input"
            />
        </div>
    </div>

    <hr class="my-2 border-gray-200 dark:border-gray-700">

    <div class="grid grid-cols-2 gap-2">
        <div>
            <label for="devices" class="block text-sm font-medium theme-text-subtle">
                {{ __('data.attributes.devices') }}
            </label>
        </div>
        <div>
            <x-wireui-select
                label="{{ __('data.attributes.devices') }}"
                placeholder="{{ __('data.attributes.devices') }}"
                multiselect
                :async-data="route('measurement-devices.get_devices', $devices)"
                wire:model="device_ids"
                option-label="name"
                option-value="id"
                class="w-full theme-input"
            />
        </div>
    </div>

    <hr class="my-2 border-gray-200 dark:border-gray-700">

    <div class="grid grid-cols-2 gap-2">
        <div>
            <label for="file" class="block text-sm font-medium theme-text-subtle">
                {{ __('data.labels.generate_report') }}
            </label>
        </div>
        <div>
            <x-wireui-button 
                wire:click="download" 
                primary 
                label="{{ __('data.actions.generate') }}" 
                class="w-full"
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
        <x-wireui-button 
            type="submit" 
            primary 
            label="{{ __('translation.placeholder.save') }}" 
            spinner
        />
    </div>
</form>