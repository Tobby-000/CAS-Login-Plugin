<?php

namespace minejufe\cas\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Facades\Option;
use App\Services\OptionForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Events\Dispatcher;
use App\Events;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use minejufe\cas\Controllers\CasLoginController;

class RegisterController extends Controller
{
    public function Register(){
        // 从session中获取CAS用户信息
        $casUser = Session::get('cas_user');
        if (!$casUser || !isset($casUser['account'])) {
            // 如果没有CAS用户信息，重定向到CAS登录
            return redirect()->route('cas.login');
        }
        return view('minejufe\cas::register',['casUsername'=>$casUser['account']]);
    }

    public function handleRegister(Request $request){
        // 从session中获取CAS用户信息
        $casUser = Session::get('cas_user');
        if (!$casUser || !isset($casUser['account'])) {
            return redirect()->route('cas.login');
        }
        $casAccount = $casUser['account'];
        $email = $casAccount . '@stu.jufe.edu.cn'; // 使用CAS账号构建邮箱
                // 再次检查是否已经注册（防止重复提交）
        if (User::where('email', $email)->exists()) {
            // 如果已经注册，直接登录
            $user = User::where('email', $email)->first();
            Auth::login($user, true);
            Session::forget('cas_user');
            return redirect('/user')->with('status', '您已注册，已自动登录。');
        }

        $id = $request->input('id');
        $password = $request->input('pwd');

        $user = new User();
        $user->email = $email;
        $user->score = option('user_initial_score');
        $user->avatar = 0;
        $user->verified = 1;
        $user->password = Hash::make($pwd);
        $user->ip = $request->ip();
        $user->player_name=$id;
        $user->nickname=$id;
        $user->permission = User::NORMAL;
        $user->register_at = Carbon::now();
        $user->last_sign_at = Carbon::now()->subDay();
        $user->save();
        Auth::login($user, true);
        // 注册成功后，清除cas_user session
        Session::forget('cas_user');
        CasLoginController::cleanup();
        return redirect('/user')->with('status', '注册成功并已登录。');
    }
}