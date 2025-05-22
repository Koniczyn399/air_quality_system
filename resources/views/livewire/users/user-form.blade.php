<div class="p-2">
    <form wire:submit.prevent="submit">
        <h3 class="text-xl font-semibold leading-tight theme-text">
            @if (isset($id))
                {{ __('users.labels.edit_form_title') }}
            @else
                {{ __('users.labels.create_form_title') }}
            @endif
        </h3>

        <hr class="my-2 theme-border">

        <div class="grid grid-cols-2 gap-2">
            <div class="col-span-1">
                <label for="name" class="theme-text">{{ __('users.attributes.name') }}</label>
            </div>
            <div class="col-span-1">
                <x-wireui-input placeholder="" wire:model="name" />
            </div>
        </div>

        <hr class="my-2 theme-border">

        <div class="grid grid-cols-2 gap-2">
            <div class="col-span-1">
                <label for="email" class="theme-text">{{ __('users.attributes.email') }}</label>
            </div>
            <div class="col-span-1">
                <x-wireui-input placeholder="" wire:model="email" />
            </div>
        </div>

        <hr class="my-2 theme-border">

        <div class="grid grid-cols-2 gap-2">
            <div class="col-span-1">
                <label for="password" class="theme-text">{{ __('users.attributes.password') }}</label>
            </div>
            <div class="col-span-1">
                <x-wireui-password placeholder="" wire:model="password" />
            </div>
        </div>

        <hr class="my-2 theme-border">

        <div class="flex justify-end pt-2">
            <x-wireui-button href="{{ route('users.index') }}" secondary class="mr-2"
                             label="{{ __('translation.placeholder.cancel') }}" />
            <x-wireui-button type="submit" primary label="{{ __('translation.placeholder.save') }}" spinner />
        </div>
    </form>
</div>