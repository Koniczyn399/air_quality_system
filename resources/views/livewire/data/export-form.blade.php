<div class="p-2">
    <form wire:submit.prevent="submit">
        <h3 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('translation.labels.data_management') }}
        </h3>

                
                
        <hr class="my-2">
        <div class="grid grid-cols-2 gap-2" style="padding-bottom: 25px;">
            <div class="">
                <label for="start_date"> {{ __('data.attributes.start_date') }}</label>
            </div>
            <div class="" style=" position: fixed; left: 40%;"  >

            <x-wireui-datetime-picker
                            wire:model.live="start_date"
                            
                            placeholder="{{ __('data.attributes.start_date') }}"
                            parse-format="YYYY-MM-DD"
                        />
                

            </div>
        </div>

        <hr class="my-2">
        <div class="grid grid-cols-2 gap-2" style="padding-bottom: 25px;">
            <div class="">
                <label for="end_date"> {{ __('data.attributes.end_date') }}</label>
            </div>
            <div class="" style=" position: fixed; left: 70%;"  >

            <x-wireui-datetime-picker
                            wire:model.live="end_date"

                            placeholder="{{ __('data.attributes.end_date') }}"
                            parse-format="YYYY-MM-DD"
                        />
                

            </div>
        </div>

        <hr class="my-2">
        <div class="grid grid-cols-2 gap-2">
            <div class="">
                <label for="devices"> {{ __('data.attributes.devices') }}</label>
            </div>
            <div class="">


            <x-wireui-select
                label="{{ __('data.attributes.devices') }}"
                placeholder="{{ __('data.attributes.devices') }}"
                multiselect
                :async-data="route('measurement-devices.get_devices', $devices)"
                wire:model="device_ids"
                option-label="name" 
                option-value="id"
            />

                

            </div>
        </div>

                
        <hr class="my-2">
            <div class="flex justify-end pt-2">
            <x-wireui-button href="{{ route('measurement-devices.index') }}" secondary class="mr-2"
            label="{{ __('translation.placeholder.cancel') }}" />
            <x-wireui-button type="submit" primary label="{{ __('translation.placeholder.save') }}" spinner />
        </div>
    </form>
  


    
</div>
