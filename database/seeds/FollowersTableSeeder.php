<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class FollowersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $users = User::all();
        $user = $users->first();
        $user_id = $user->id;

        $users = $users->slice(1);

        // 第一个用户关注所有用户
        $user->follow($users->pluck("id")->toArray());

        // 除了第一个用户之外，所有用户都关注第一个用户
        foreach ($users as $user) {
            $user->follow($user_id);
        }
    }
}
