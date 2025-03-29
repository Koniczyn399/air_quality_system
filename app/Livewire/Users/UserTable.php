<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class UserTable extends PowerGridComponent
{
    public string $tableName = 'user-table-ksr2tk-table';

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return User::query()->with('roles');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('email')
            ->add('roles', function ($user){
                return $user->roles->pluck('name')->join(', ');
            }
            
            )
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make(__('users.attributes.id'), 'id')
            ->sortable()
            ->searchable(),
            Column::make(__('users.attributes.name'), 'name')
                ->sortable()
                ->searchable(),

            Column::make(__('users.attributes.email'), 'email')
                ->sortable()
                ->searchable(),

            Column::make(__('users.attributes.roles'), 'roles')
                ->sortable()
                ->searchable(),

            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::make('Created at', 'created_at')
                ->sortable()
                ->searchable(),

    
    
            Column::action(__('translation.attributes.actions')),
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert('.$rowId.')');
    }

    #[\Livewire\Attributes\On('remove_user')]
    public function remove_user($id): void
    {
        //$this->authorize('delete', User::findOrFail($id));
        User::findOrFail($id)->delete();
    }

    public function actions(User $user): array
    {
        return [

            Button::add('edit_user')
                ->slot(Blade::render('<x-wireui-icon name="pencil" class="w-5 h-5" mini />'))
                ->tooltip(__('users.actions.edit_user'))
                ->class('text-yellow-500')
                ->route('users.edit', [$user]),

            Button::add('remove_user')
                ->slot(Blade::render('<x-wireui-icon name="x-mark"  class="w-5 x h-5" mini />'))
                ->tooltip(__('users.actions.remove_user'))
                ->class('text-red-500')
                ->dispatch('remove_user', ['id' => $user->id]),
        ];
    }

    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
