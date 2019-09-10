<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionsController extends Controller
{

    public function __construct()
    {
        // 未登陆的用户才能访问，登陆用户默认跳转到"/home"，可以在RedirectAuthenticated中间件中修改
        $this->middleware("guest",[
            "only" => ["create"]
        ]);
    }

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
        if(Auth::attempt($info, $request->has("remember"))) { // 数组的键必须有一个是password，并且数组的第一个键值用于查询数据库，因此第一个键值必须是唯一可以定位到用户的
            // 记住我，默认的登陆状态被保持2小时，"记住我"登陆状态保持5年


            // 未激活的用户跳转到首页，由于Auth::attempt在用户校验通过之后就默认登陆了，监测到未激活时记得登出。
            if(Auth::user()->activated) {
                session()->flash("success","欢迎回来！");

                $defaultUrl = route("users.show", Auth::user());
                //intended将页面重定向到上次请求访问的页面，如果上次请求访问的页面为空则跳转到defaultUrl页面
                return redirect()->intended($defaultUrl);
            } else {
                Auth::logout();
                session()->flash("warning","你的账号未激活，请检查邮件里的注册邮件进行激活。");
                return redirect("/");
            }


        } else {
            session()->flash("danger","很抱歉，您的邮箱和密码不匹配");
            // 表单数据验证错误会自动将表单闪存，但是以下方式不会，因此需要调用withInput()方法，old函数才能获取到上一次提交的表单数据
            return redirect()->back()->withInput();
        }
    }

    // 遵循restful风格的api，将数据看作是资源，用uri来定位资源。使用get，post，patch，delete来对资源进行增删改查
    // 由于浏览器不支持delete，patch请求，在laravel中可以使用隐藏域来伪造delete请求："method_field('DELETE')"
    public function destroy() {
        Auth::logout();
        session()->flash("success","您已成功退出！");
        return redirect()->route("login");
    }

}
