<?php

use App\Services\Hook;
use App\Services\Plugin;
use Illuminate\Contracts\Events\Dispatcher;
use minejufe\cas\Controllers\CasLoginController;

return function (Plugin $plugin) {
    // 注册路由
    require __DIR__.'/routes/web.php';
    
    // // 在登录页面添加CAS登录选项
    // Hook::addView('auth.login', function () {
    //     return view('minejufe::cas-login-option');
    // });
    
    // // 在注册页面添加CAS注册选项
    // Hook::addView('auth.register', function () {
    //     return view('minejufe::cas-register-option');
    // });
    
};