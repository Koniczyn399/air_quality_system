<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
       
       
        //$this->authorize('viewAny', User::class);
        
     
            return view(
                'users.index',
                [
                    'users' => User::paginate(
                        config('pagination.default')
                    )
            
                ]
            );

    }
}
