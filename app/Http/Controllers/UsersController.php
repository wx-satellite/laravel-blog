<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



/* 用户密码重置
 *  用户点击重设密码链接并跳转到重设密码页面；
    在重设密码页面输入邮箱信息并提交；
    控制器通过该邮箱查找到指定用户并为该用户生成一个密码令牌，接着将该令牌以链接的形式发送到用户提交的邮箱上；
    用户查看自己个人邮箱，点击重置密码链接跳转到重置密码页面；
    用户在该页面输入自己的邮箱和密码并提交；
    控制器对用户的邮箱和密码重置令牌进行匹配，匹配成功则更新用户密码；
 */

/*
 * 关于数据统计：本案例直接使用count对数据统计但是在大型系统中为了节省服务器资源和提高数据库查询效率会选择冗余一个计数字段，每次对模型
 * 增加删除都更新这个字段
 */
class UsersController extends Controller
{

    public function __construct()
    {
        // 如果用户未登陆访问update，edit默认会被重定向到"/login"页面，可以在Authenticate中间件中修改
        $this->middleware("auth",[
            "except" => ["show", "create", "store","index","confirmEmail"]
        ]);


        // 未登录的用户才能访问create
        $this->middleware("guest",[
            "only" => ["create"]
        ]);
    }


    // "{{}}"转义输出，"{!! !!}"原生输出
    // 数据库内容自动填充：UserTableSeeder
    // 局部视图：@include("user._user")继承了当前行的作用域，因此在_user.blade.php文件中可以访问$user变量
    // @can 在模板中使用授权策略
    public function index($pageSize=10) {
        $users = User::query()->paginate($pageSize);
        return view("users.index",compact("users"));
    }


    public function create() {
        return view("users.create");
    }


    // 利用了laravel的"隐性路由模型绑定"，直接读取对应id的模型$user，找不到则报404
    // "隐性路由模型绑定"有一个约定优于配置的注意点：一般路由片段是模型的小写，例如{user}，并且方法的参数也是$user。
    // 实际上经过测试：只要路由片段和变量名保持一致就好了，例如:{a}对应的变量"User $a"
    public function show(User $user, $pageSize=15) {

        $statuses = $user->statuses()->orderBy("created_at","desc")->paginate($pageSize);

        // compact("user","statuses") 等价于 ["user"=>$user,"statuses"=>$statuses]
        // 关于Carbon的使用"$status->created_at->diffForHumans()"
        return view("users.show", compact('user',"statuses"));
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
//        Auth::login($user);
//        session()->flash("success","欢迎，您将在这里开启一段新的旅程~");
//        // 等效于 redirect()->route("users.show",[$user->id])，下述写法是"约定优于配置"的一种写法，route方法会自动获取模型的id主键
//        return redirect()->route("users.show",[$user]);


        $user->sendEmailConfirmationTo($user);
        session()->flash("success","注册成功，注意查收激活邮件");
        return redirect("/");



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


    // 授权策略
    public function destroy(User $user) {
        $this->authorize("destroy", $user);
        $user->delete();
        session()->flash("success","删除用户成功！");
        return redirect()->back();

    }


    // 激活
    // 邮件通知：25端口对应tls，465端口对应ssl加密
    public function confirmEmail($token) {

        // 找不到就返回404响应，类似findOrFail
        $user = User::query()->where("activation_token", $token)->firstOrFail();


        $user->activated = true;
        $user->activation_token = null; // 激活标志置为null，防止用户多次操作
        $user->save();

        // 自动登陆
        Auth::login($user);


        session()->flash("success","恭喜你，激活成功！");
        return redirect()->route("users.show",[$user]);

    }


    public function followings(User $user, $pageSize=15) {
        $users = $user->followings()->paginate($pageSize);
        $title = $user->name."关注的人";
        return view("users.follow",compact("users","title"));
    }

    public function followers(User $user, $pageSize=15) {
        $users = $user->followers()->paginate($pageSize);
        $title = $user->name."的粉丝";
        return view("users.follow",compact("users","title"));
    }
}
