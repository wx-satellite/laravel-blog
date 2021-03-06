<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatusesController extends Controller
{
    //
    public function __construct()
    {
        // 由于发布微博和删除微博都是需要登陆的，因此直接写"$this->middleware('auth')"即可不需要传递第三个参数
        $this->middleware("auth");
    }


    public function store(Request $request) {
        $data = $this->validate($request,[
            "content" => "required|max:140"
        ]);


        // 通过模型关联创建微博不需要指定user_id，会自动和用户进行关联
        Auth::user()->statuses()->create([
            "content" => $data["content"]
        ]);

        session()->flash("success","发布成功！");
        return back();
    }


    public function destroy(Status $status) {
        // 没有权限时报403
        $this->authorize("destroy", $status);
        $status->delete();
        session()->flash("success","删除成功！");
        return redirect()->back();
    }
}
