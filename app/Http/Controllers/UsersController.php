<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{

    public function __construct()
    {
        // 如果用户未登陆访问update，edit默认会被重定向到"/login"页面，可以在Authenticate中间件中修改
        $this->middleware("auth",[
            "except" => ["show", "create", "store"]
        ]);


        // 未登录的用户才能访问create
        $this->middleware("guest",[
            "only" => ["create"]
        ]);
    }

    public function create() {
        return view("users.create");
    }


    // 利用了laravel的"隐性路由模型绑定"，直接读取对应id的模型$user，找不到则报404
    public function show(User $user) {
        // compact("user") 等价于 ["user"=>$user]
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

        // 注册用户自动登陆
        Auth::login($user);

        session()->flash("success","欢迎，您将在这里开启一段新的旅程~");
        // 等效于 redirect()->route("users.show",[$user->id])，下述写法是"约定优于配置"的一种写法，route方法会自动获取模型的id主键
        return redirect()->route("users.show",[$user]);
    }

    public function edit(User $user) {
        $this->authorize("update", $user);
        return view("users.edit",compact('user'));
    }

    public function update(User $user,Request $request) {
        $this->authorize("update", $user);
        $this->validate($request, [
            "name" => "required|max:255",
            "password" => "nullable|min:6|confirmed" // 当用户提供空白密码时也会通过验证
        ]);

        $data = ["name" => $request->name];

        if($request->password) {
            $data["password"] = bcrypt($request->password);
        }

        $user->update($data);

        session()->flash("success","个人资料更新成功！");

        // 或者： route("users.show",$user)
        return redirect()->route("users.show",[$user]);
    }
}
