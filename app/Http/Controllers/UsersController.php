<?php

namespace App\Http\Controllers;

use App\User;

class UsersController extends Controller
{
    public function create()
    {
        return view('users.create');
    }

    public function show(User $user)
    {
        return view('users.show', [
            'user' => $user
        ]);
    }
}
