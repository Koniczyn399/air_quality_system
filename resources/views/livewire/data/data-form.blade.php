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
                label="{{ __('translation.actions.next_page') }}" 
            
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
            <x-wireui-button 
                a 
                href="{{ route('data.import') }}" 
                primary 
                label="{{ __('translation.actions.next_page') }}" 
            
            />
        </div>
</div>
</form>
