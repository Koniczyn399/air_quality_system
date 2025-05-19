<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function index()
    {

        $this->authorize('viewAny', User::class);

        return view(
            'users.index',
            [
                'users' => User::paginate(
                    config('pagination.default')
                ),

            ]
        );

    }

    public function create()
    {
        // $this->authorize('create', User::class);
        return view(
            'users.form'
        );
    }

    public function edit(User $user)
    {
        // $this->authorize('update', $user);

        return view(
            'users.form',
            [
                'user' => $user,
            ]
        );
    }
}
