<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;
use WireUi\Traits\WireUiActions;

class UserForm extends Component
{
    use WireUiActions;

    public User $user;

    public $id = null;
    public $name = "";
    public $email = "";
    public $password = "";


    public function mount(User $user = null)
    {

        //dd($manufacturer);

        $this->user = $user;


        if (isset($user->id)) {
            $this->id = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            // $this->password = $user->password;
        }
    }

    public function submit()
    {
        // if (isset($this->user->id)) {
        //     $this->authorize('update', $this->user);
        // } else {
        //     $this->authorize('create', User::class);
        // }

        User::updateOrCreate(
            ['id' => $this->id],
            $this->validate()
        );



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
                'min:2',

            ],

 
        ];
    }

    public function validationAttributes()
    {
        return [
            'name' => Str::lower(__('users.attributes.name')),
            'last_name' => Str::lower(__('users.attributes.last_name')),
        ];
    }
    public function render()
    {
        return view('livewire.users.user-form');
    }
}
