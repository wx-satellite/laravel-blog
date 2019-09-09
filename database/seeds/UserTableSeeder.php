<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = factory(User::class)->times(50)->make();
        User::query()->insert($users->makeVisible(["password","remember_token"])->toArray());
        $user = User::query()->find(1);
        $user->email = "1453085314@qq.com";
        $user->password = bcrypt("123456");
        $user->save();
    }
}
