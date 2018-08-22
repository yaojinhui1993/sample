<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $users = factory(User::class)->times(50)->make();
        User::insert($users->makeVisible(['password', 'remember_token'])->toArray());

        $user = User::find(1);
        $user->name = 'John';
        $user->email = 'john@example.com';
        $user->password = bcrypt('password');
        $user->is_admin = true;
        $user->activated = true;
        $user->save();
    }
}
