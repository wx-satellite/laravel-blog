<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{

    public function create() {
        return view("users.create");
    }


    public function show(User $user) {
        return view("users.show", compact('user'));
    }

    public function store(Request $request) {
        $this->validate($request,[
            "name" => "required|max:50",
            "email" => "required|email|unique:users|max:255",
            // confirmed验证两次密码是否一致，表单的确认密码字段必须是"password_confirmation"
            "password" => "required|confirmed|min:6"
        ]);
        return ;
    }
}
