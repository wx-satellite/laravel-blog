<?php


use Illuminate\Support\Facades\Route;

Route::get('/', 'StaticPagesController@home')->name("home");
Route::get('/help', 'StaticPagesController@help')->name("help");
Route::get('/about', 'StaticPagesController@about')->name("about");


/**** 用户相关  ****/

Route::get("signup", "UsersController@create")->name("signup");