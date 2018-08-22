<?php

/*
 * presets: symfony
 */

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function create()
    {
        return view('users.create');
    }

    public function show(User $user)
    {
        return view('users.show', [
            'user' => $user,
        ]);
    }

    public function store()
    {
        $this->validate(request(), [
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6',
        ]);

        $user = User::create([
            'name' => request()->name,
            'email' => request()->email,
            'password' => bcrypt(request()->password),
        ]);

        Auth::login($user);

        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');

        return redirect()->route('users.show', [$user]);
    }
}
