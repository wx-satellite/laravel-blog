<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */


    // 手动注册"授权策略"，laravel5.8之后支持自动注册
    protected $policies = [
         'App\Models\User' => 'App\Policies\UserPolicy',
        "App\Models\Status" => "App\Policies\StatusPolicy",
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

    }
}
