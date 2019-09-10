<?php

namespace App\Http\Controllers;

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

        Auth::user()->statuses()->create([
            "content" => $data["content"]
        ]);

        session()->flash("success","发布成功！");
        return back();
    }
}
