<?php


use Illuminate\Support\Facades\Route;

Route::get('/', 'StaticPagesController@home')->name("home");
Route::get('/help', 'StaticPagesController@help')->name("help");
Route::get('/about', 'StaticPagesController@about')->name("about");


/**** 用户相关  ****/

Route::get("signup", "UsersController@create")->name("signup");
Route::resource("users","UsersController");

Route::get("login","SessionsController@create")->name("login");
Route::post("login","SessionsController@store")->name("login");
Route::delete("logout","SessionsController@destroy")->name("logout");
// 邮件激活
Route::get("signup/confirm/{token}", "UsersController@confirmEmail")->name("confirm_email");