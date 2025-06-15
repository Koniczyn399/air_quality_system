<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Component;
use WireUi\Traits\WireUiActions;
use Spatie\Permission\Models\Role;

class UserForm extends Component
{
    use WireUiActions;

    public User $user;

    public $id = null;

    public $name = '';

    public $email = '';

    public $password = '';

    public $roles = [];

    public $selectedRole = '';

    public function mount(?User $user = null)
    {

        // dd($manufacturer);

        $this->user = $user;
        $this->roles = Role::where('guard_name', 'web')
                ->get(['id', 'name'])
                ->map(fn($role) => ['id' => $role->id, 'name' => $role->name])
                ->toArray();
                
        if (isset($user->id)) {
            $this->id = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            // $this->password = $user->password;
            if ($user->roles->isNotEmpty()) {
                $this->selectedRole = $user->roles->first()->id;
            }
        } else {
            // Ustaw domyślną rolę dla nowego użytkownika, np. "user" dla strażnika 'web'
            $defaultUserRole = Role::where('name', \App\Enums\Auth\RoleType::USER->value)
                                   ->where('guard_name', 'web') // Jawnie określ guard
                                   ->first();
            if ($defaultUserRole) {
                $this->selectedRole = $defaultUserRole->id;
            }
        }
    }

    public function submit()
    {   
        $this->validate();
        // if (isset($this->user->id)) {
        //     $this->authorize('update', $this->user);
        // } else {
        //     $this->authorize('create', User::class);
        // }

        $user = User::updateOrCreate(
            ['id' => $this->id],
            [
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password ? \Illuminate\Support\Facades\Hash::make($this->password) : $this->user->password, // Haszuj hasło tylko jeśli zostało podane
            ]
        );

        if ($this->selectedRole) {
            // 2. Jawnie określ strażnika 'web' podczas wyszukiwania roli
            $role = Role::findById($this->selectedRole, 'web');
            $user->syncRoles($role);
        }

        return $this->redirect(route('users.index'));
    }

    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',

            ],

            'password' => [
                'required',
                'string',
                'min:2',

            ],

            'email' => [
                'required',
                'string',
                'email',
                'min:2',

            ],

            'selectedRole' => [
                'required',
                'exists:roles,id',
            ],

        ];
    }

    public function validationAttributes()
    {
        return [
            'name' => Str::lower(__('users.attributes.name')),
            'last_name' => Str::lower(__('users.attributes.last_name')),
            'selectedRole' => Str::lower(__('users.attributes.role')),
        ];
    }

    public function render()
    {
        return view('livewire.users.user-form');
    }
}
