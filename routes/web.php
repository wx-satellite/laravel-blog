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
// 重置密码
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

// 发表或者删除微博（只需要"store"和"destroy"两个动作的路由）
Route::resource("statuses","StatusesController",["only"=>["store","destroy"]]);

// 关注者列表
Route::get("users/{user}/followings","UsersController@followings")->name("users.followings");
// 粉丝列表
Route::get("users/{user}/followers","UsersController@followers")->name("users.followers");

// 关注
Route::post("users/followers/{user}","FollowersController@store")->name("followers.store");
// 取关
Route::delete("users/followers/{user}","FollowersController@destroy")->name("followers.destroy");