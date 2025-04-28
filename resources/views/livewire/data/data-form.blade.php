<div class="p-2">
    <form wire:submit.prevent="submit">
        <h3 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('translation.labels.data_management') }}
        </h3>

                
        
        <hr class="my-2">
        <div class="grid grid-cols-2 gap-2">
            <div class="">
                <label for="file">{{ __('translation.labels.export_file') }}</label>
            </div>
            <div class="">
                <x-wireui-button  a href="{{ route('data.export') }}" primary label="{{ __('translation.placeholder.save') }}"  />
                
            </div>
        </div>

        <hr class="my-2">
        <div class="grid grid-cols-2 gap-2">
            <div class="">
                <label for="file">{{ __('data.labels.generate_report') }}</label>
            </div>
            <div class="">
                <x-wireui-button  a href="{{ route('data.invoice') }}" primary label="{{ __('data.actions.generate') }}"  />
                
            </div>
        </div>
                
        <hr class="my-2">
        <div class="grid grid-cols-2 gap-2">
            <div class="">
                <label for="file"> {{ __('translation.labels.upload_file') }}</label>
            </div>
            <div class="">
            <input type="file" wire:model="file">
 
                    @error('file') <span class="error">{{ $message }}</span> @enderror
                    <x-wireui-button type="submit" primary label="{{ __('translation.placeholder.save') }}" spinner />
            </div>
        </div>


                
        <hr class="my-2">
            <div class="flex justify-end pt-2">
            <x-wireui-button href="{{ route('measurement-devices.index') }}" secondary class="mr-2"
            label="{{ __('translation.placeholder.cancel') }}" />
        </div>
    </form>


    @if ($file) 
        <hr class="my-2">
        <div class="grid grid-cols-2 gap-2">
        
            {{ print($meaurements) }}
        
        </div>
    @endif
    
</div>
