<div class="p-2">
    <form wire:submit.prevent="submit">
        <h3 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('translation.labels.upload_file') }}
        </h3>

                
                
        <hr class="my-2">
        <div class="grid grid-cols-2 gap-2">
            <div class="">
                <label for="file">{{ __('translation.misc.file') }}</label>
            </div>
            <div class="">
            <input type="file" wire:model="file">
 
                    @error('file') <span class="error">{{ $message }}</span> @enderror

            </div>
        </div>


                
        <hr class="my-2">
            <div class="flex justify-end pt-2">
            <x-wireui-button href="{{ route('measurement-devices.index') }}" secondary class="mr-2"
            label="{{ __('translation.placeholder.cancel') }}" />
            <x-wireui-button type="submit" primary label="{{ __('translation.placeholder.save') }}" spinner />
        </div>
    </form>
    @if ($file) 
        <hr class="my-2">
        <div class="grid grid-cols-2 gap-2">
        
            {{ print($meaurements) }}
        
        </div>
    @endif
    
</div>
