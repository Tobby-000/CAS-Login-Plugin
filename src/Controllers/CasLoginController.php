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
use phpCAS;
use App\Services\Facades\Option;
use Illuminate\Support\Facades\Session;

class CasLoginController extends Controller
{ 
    public function __construct()
    {
        $this->BASE_PATH = Option::get('base_path','https://cas.example.com/cas');
        $this->LOGIN_URI = Option::get('login_uri','/login');
        $this->LOGOUT_URI = Option::get('logout_uri','/logout');
        $this->WELCOME_URI = Option::get('welcome_uri','/index.html');
        $this->REDIRECT_KEY = Option::get('redirect_key','service');
        $this->LOGIN_KEY = Option::get('session_login_key','isSupwisdomCasLogin');
        $this->LOGIN_USER_KEY = Option::get('session_user_key','supwisdomCasLoginUser');

        // 初始化phpCAS
        $url = parse_url($this->BASE_PATH);
        $host = $url['host'];
        $port = $url['port'] ?? ($url['scheme'] === 'https' ? 443 : 80);
        $path = $url['path'] ?? '';
        phpCAS::client(CAS_VERSION_2_0, $host, $port, $path, '');
        phpCAS::setNoCasServerValidation();
        if(debug)
            phpCAS::setDebug(storage_path('logs/cas.log'));
        // 如需设置超时，确保phpCAS版本支持
        // if (method_exists('phpCAS', 'setClientTimeout')) phpCAS::setClientTimeout(10);
    }
    
    public function Login()
    {
        if (!Session::has($this->LOGIN_KEY) || !Session::get($this->LOGIN_KEY)) {
            phpCAS::forceAuthentication();
            $loginUser = phpCAS::getAttributes();
            $loginUser['account'] = phpCAS::getUser();
            
            $loginResult = $this->doLogin($loginUser);
            if ($loginResult === true) {
                Session::put($this->LOGIN_USER_KEY, $loginUser);
                Session::put($this->LOGIN_KEY, true);
                Session::save();
                return $this->redirectTargetUrl();
            } elseif ($loginResult instanceof \Illuminate\Http\RedirectResponse) {
                return $loginResult;
            } else {
                return redirect($this->BASE_PATH . $this->LOGOUT_URI);
            }
        } else {
            return $this->redirectTargetUrl();
        }
    }
    
    private function redirectTargetUrl()
    {
        if (request()->has($this->REDIRECT_KEY)) {
            return redirect(request($this->REDIRECT_KEY));
        } else {
            return redirect($this->BASE_PATH . $this->WELCOME_URI);
        }
    }
    
    private function doLogin(array $loginUser = array())
    {
        $user = User::where('email', $loginUser['account'].'@stu.jufe.edu.cn')->first();
        
        if (!$user) {
            Session::put('cas_user', $loginUser);
            return redirect()->route('cas.register');
        } else {
            $this->cleanUp();
            Auth::login($user, true);
            return true;
        }
    }
    
    public function cleanUp(){
        Session::forget($this->LOGIN_KEY);
        Session::forget($this->LOGIN_USER_KEY);
        phpCAS::logout();
    }
}