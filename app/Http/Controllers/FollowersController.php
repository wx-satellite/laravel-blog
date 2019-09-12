<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowersController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }

    // 关注
    public function store(User $user) {
        // 授权策略：当前用户不能关注或者取关自己
        $this->authorize("follow", $user);

        if(!Auth::user()->isFollowing($user->id)) {
            Auth::user()->follow($user->id);
        }
        return redirect()->route("users.show",$user);


    }


    // 取关
    public function destroy(User $user) {
        $this->authorize("follow", $user);

        if(Auth::user()->isFollowing($user)) {
            Auth::user()->unfollow($user->id);
        }
        return redirect()->route("users.show",$user);
    }
}

