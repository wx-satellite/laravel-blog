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

        // 验证失败时，laravel会自动将错误绑定到视图上，使用$errors在视图中获取错误

        $user = User::create([
            // request()->input("name","")
            "name" => $request->name,
            "email" => $request->email,
            "password" => bcrypt($request->password)
        ]);
        session()->flash("success","欢迎，您将在这里开启一段新的旅程~");
        // 等效于 redirect()->route("users.show",[$user->id])，下述写法是"约定优于配置"的一种写法，route方法会自动获取模型的id主键
        return redirect()->route("users.show",[$user]);
    }
}
