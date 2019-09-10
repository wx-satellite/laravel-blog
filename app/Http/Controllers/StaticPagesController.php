<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaticPagesController extends Controller
{

    // 视图：局部视图的引入"@include"，这里有一个约定优于配置，即局部视图以"_"开头
    public function home()
    {
        $feed_items = [];

        // Auth::check方法判断用户是否登陆
        if(Auth::check()) {
            $pageSize = 15;
            $feed_items = Auth::user()->feeds()->paginate($pageSize);
        }
        return view('static_pages.home',compact("feed_items"));
    }

    public function help()
    {
        return view('static_pages.help');
    }

    public function about()
    {
        return view('static_pages.about');
    }
}
