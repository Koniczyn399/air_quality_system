<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Blade;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use Illuminate\Support\Facades\Auth;
use App\Enums\Auth\RoleType;

final class UserTable extends PowerGridComponent
{
    public string $tableName = 'user-table-ksr2tk-table';

    public function setUp(): array
    {
        // $this->showCheckBox();

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
            ->add('roles', function ($user) {
                return $user->roles->pluck('name')->join(', ');
            }

            )
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            // Column::make(__('users.attributes.id'), 'id')
            //     ->sortable()
            //     ->searchable(),
            Column::make(__('users.attributes.name'), 'name')
                ->sortable()
                ->searchable(),

            Column::make(__('users.attributes.email'), 'email')
                ->sortable()
                ->searchable(),

            Column::make(__('users.attributes.roles'), 'roles')
                ->sortable()
                ->searchable(),

            // Column::make('Created at', 'created_at_formatted', 'created_at')
            //     ->sortable(),

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
        // $this->authorize('delete', User::findOrFail($id));
        User::findOrFail($id)->delete();
    }

    public function actions(User $user): array
    {
        $actions = [];
        $user_zalogowany = Auth::user(); // Pobieramy zalogowanego użytkownika

        // Sprawdzamy, czy użytkownik ma rolę 'ADMIN' lub 'MAINTEINER' (Serwisant)
        // Używamy RoleType::ADMIN->value i RoleType::MAINTEINER->value
        /** @var \App\Models\User $user_zalogowany */
        if ($user_zalogowany && ($user_zalogowany->hasRole(RoleType::ADMIN->value) || $user_zalogowany->hasRole(RoleType::MAINTEINER->value))) {
            $actions[] = Button::add('edit_user')
                ->slot(Blade::render('<x-wireui-icon name="wrench" class="w-5 h-5" mini />'))
                ->tooltip(__('users.actions.edit_user'))
                ->class('text-yellow-500')
                ->route('users.edit', [$user]);

            $actions[] = Button::add('delete')
                ->slot(Blade::render('<x-wireui-icon name="trash" class="w-5 h-5" />'))
                ->tooltip('Usuń')
                ->class('text-red-500 hover:text-red-700')
                ->dispatch('delete_device', [
                    'id' => $user->id,
                    'confirm' => [
                        'title' => 'Potwierdzenie usunięcia',
                        'description' => 'Czy na pewno chcesz usunąć tego użytkownika?',
                        'accept' => [
                            'label' => 'Tak, usuń',
                            'method' => 'delete',
                            'params' => ['users' => $user->id],
                        ],
                        'reject' => [
                            'label' => 'Anuluj',
                        ],
                    ],
                ]);
        }

        return $actions;
    }

    #[\Livewire\Attributes\On('delete_confirmed')]
    public function deleteConfirmed($id): void
    {
        $device = User::find($id);
        if ($device) {
            $device->delete();
            $this->dispatch('showToast', type: 'success', message: 'Użytkownik został usunięty');
        } else {
            $this->dispatch('showToast', type: 'error', message: 'Nie znaleziono użytkownika do usunięcia');
        }
    }
}
