<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionsController extends Controller
{


    public function create() {
        return view("sessions.create");
    }

    public function store(Request $request) {
        // 验证失败之后，会自动重定向到表单提交页面，并且提交的数据会被自动闪存，用old函数可以获取
        // 验证成功，返回数组
        $info = $this->validate($request,[
            "email" => "required|email|max:255",
            "password" => "required"
        ]);

        // attempt第一个参数是一个数组，会根据数组的第一个键值，本案例即email查找到用户，
        // 再根据传入的"password"值进行哈希加密，然后与数据库中 password 字段中已加密的密码进行匹配
        // 不相等或者用户不存在都返回false
        if(Auth::attempt($info)) { // 数组的键必须有一个是password，并且数组的第一个键值用于查询数据库，因此第一个键值必须是唯一可以定位到用户的

            session()->flash("success","欢迎回来！");
            return redirect()->route("users.show",[Auth::user()]);
        } else {
            session()->flash("danger","很抱歉，您的邮箱和密码不匹配");
            // 表单数据验证错误会自动将表单闪存，但是以下方式不会，因此需要调用withInput()方法，old函数才能获取到上一次提交的表单数据
            return redirect()->back()->withInput();
        }
        return;
    }
}
