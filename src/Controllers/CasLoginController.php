<?php

namespace minejufe\cas\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Events\Dispatcher;
use App\Events;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use apereo\PhpCAS;
use minejufe\cas\Controllers\RegisterController

class LoginController extends Controller
{ 

    public function Login()
    {
        // 后面的代码都不用看
        if (!isset($_SESSION[LOGIN_KEY]) || !$_SESSION[LOGIN_KEY]) {
            // 判断是否登录、未登录跳转登录页面登录、
            phpCAS::forceAuthentication();
            // 获取登录用户信息
            $loginUser = phpCAS::getAttributes();
            $loginUser['account'] = phpCAS::getUser();
            if (isset($loginUser['account']) && doLogin($loginUser)) {
                $_SESSION[LOGIN_USER_KEY] = $loginUser;
                $_SESSION[LOGIN_KEY] = true;
                session_commit();
                redirectTargetUrl();
            } 
            else {
                header('Location: ' . BASH_PATH . LOGOUT_URI);
            }
        } 
        else {
            redirectTargetUrl();
        }

    }
    function redirectTargetUrl()
        {
            // 如果存在参数targetUrl 则登录成功后跳转
            if (isset($_REQUEST[REDIRECT_KEY])) {
                header('Location: ' . $_REQUEST[REDIRECT_KEY]);
            } else {
                header('Location: ' . BASH_PATH . WELCOME_URI);
            }
    }
    function doLogin(array $loginUser = array())
    {
        // 业务系统的登录逻辑   开始

        // Example
        $_SESSION['loginUser'] = $loginUser;
        // TODO
        $user = User::where('email', $loginUser['account'].'@stu.jufe.edu.cn')->first();
        if (!$user) {
            //return view('minejufe\cas::register');
            session(['cas_user' => $loginUser]);
            // 重定向到注册页面
            return redirect()->route('cas.register');
        }
        else {
            $this->cleanUp();
            Auth::login($user, true);
        }
        return true;
        // 业务系统的登录逻辑   结束
    }
    public function cleanUp(){
        phpCAS::logout();
    }
}