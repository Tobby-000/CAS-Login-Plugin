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
use Illuminate\Support\Facades\URL;

class CasLoginController extends Controller
{ 
    public function __construct()
    {
        $this->BASE_PATH = Option::get('base_path','https://cas.example.com/cas');
        $this->APP_URL = rtrim(Option::get('app_url', 'https://mc.jxufe.edu.cn'), '/');
        $this->LOGIN_URI = Option::get('login_uri','/login');
        $this->LOGOUT_URI = Option::get('logout_uri','/logout');
        $this->WELCOME_URI = Option::get('welcome_uri','/index.html');
        $this->REDIRECT_KEY = Option::get('redirect_key','service');
        $this->LOGIN_KEY = Option::get('session_login_key','isSupwisdomCasLogin');
        $this->LOGIN_USER_KEY = Option::get('session_user_key','supwisdomCasLoginUser');

        $url = parse_url($this->BASE_PATH);
        $host = $url['host'];
        $port = $url['port'] ?? ($url['scheme'] === 'https' ? 443 : 80);
        $path = $url['path'] ?? '';
        $serviceBaseUrl = $this->APP_URL;
        \phpCAS::client(CAS_VERSION_2_0, $host, $port, $path, $serviceBaseUrl);
        \phpCAS::setNoCasServerValidation();
        
        try {
            \phpCAS::handleLogoutRequests();
        } catch (\Throwable $e) {
            \Log::warning('phpCAS handleLogoutRequests not available: ' . $e->getMessage());
        }
        if (config('app.debug')) {
            try {
                \phpCAS::setDebug(storage_path('logs/cas.log'));
            } catch (\Throwable $e) {
                \Log::warning('phpCAS setDebug failed: ' . $e->getMessage());
            }
        }
    }
    
    public function Login()
    {
        $incomingTarget = request($this->REDIRECT_KEY) ?: request('target');
        if ($incomingTarget) {
            Session::put('cas_redirect_target', $incomingTarget);
            Session::save();
        }
        $base = URL::current();
        $query = request()->query();
        unset($query['ticket']);  
        
        if ($incomingTarget) {
            $query[$this->REDIRECT_KEY] = $incomingTarget;
            if ($this->REDIRECT_KEY !== 'target') {
                $query['target'] = $incomingTarget;
            }
        }
        
        $serviceUrl = $base . (empty($query) ? '' : ('?' . http_build_query($query)));
        try {
            \phpCAS::setFixedServiceURL($serviceUrl);
        } catch (\Throwable $e) {
            \Log::warning('phpCAS setFixedServiceURL failed: ' . $e->getMessage());
        }

        try { 
            \phpCAS::forceAuthentication();
            
            // 认证成功后处理登录逻辑
            $loginUser = \phpCAS::getAttributes();
            $loginUser['account'] = \phpCAS::getUser();
            
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
        } catch (\Throwable $e) {
            \Log::error('CAS认证失败: ' . $e->getMessage());
            return redirect($this->BASE_PATH . $this->LOGOUT_URI);
        }
    }
    private function redirectTargetUrl()
    {
        $target = Session::pull('cas_redirect_target');
        if ($target) {
            return redirect($target);
        }
        if (request()->has($this->REDIRECT_KEY)) {
            return redirect(request($this->REDIRECT_KEY));
        }
        return redirect("https://mc.jxufe.edu.cn". '/' . ltrim($this->WELCOME_URI, '/'));
    }
    
    private function doLogin(array $loginUser = array())
    {
        $emailSuffix = Option::get('email_suffix', '@stu.jxufe.edu.cn');
        $email = $loginUser['account'] . $emailSuffix;
        $user = User::where('email', $email)->first();
        
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
        session_destroy(); 
    }
}