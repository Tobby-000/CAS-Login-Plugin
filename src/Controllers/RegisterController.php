<?php

namespace minejufe\cas\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Facades\Option;
use App\Services\OptionForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\Player;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Events\Dispatcher;
use App\Events;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use minejufe\cas\Controllers\CasLoginController;
use Illuminate\Support\Facades\Session;

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
            return redirect()->route('temp.login');
        }
        $casAccount = $casUser['account'];
        $emailSuffix = Option::get('email_suffix', '@stu.jxufe.edu.cn');
        $email = $casAccount . $emailSuffix; // 使用CAS账号构建邮箱
        // 再次检查是否已经注册（防止重复提交）
        if (User::where('email', $email)->exists()) {
            // 如果已经注册，直接登录
            $user = User::where('email', $email)->first();
            Auth::login($user, true);
            Session::forget('cas_user');
            return redirect('/user')->with('status', '您已注册，已自动登录。');
        }
            // 验证输入
            $validated = $request->validate([
                'id' => 'required|string|max:20',
                'pwd' => 'required|string|min:6|max:20',
                'playerid'=>'required|string|max:20|unique:players,name'
            ]);
            $id = $validated['id'];
            $password = $validated['pwd'];
            $playerid = $validated['playerid'];
            // 创建新用户
        try{
            $user = new User();
            $user->email = $email;
            $user->score = option('user_initial_score');
            $user->avatar = 0;
            $user->verified = 1;
            $user->password = Hash::make($password);
            $user->ip = $request->ip();
            $user->nickname=$id;
            $user->permission = User::NORMAL;
            $user->register_at = Carbon::now();
            $user->last_sign_at = Carbon::now()->subDay();
            $user->save();
            // 创建新角色
            $player=new Player();
            $player->name=$playerid;
            $player->uid=$user->uid;
            $player->tid_skin = 0;
            $player->save();
            Auth::login($user, true);
                    // 注册成功后，清除cas_user session
            Session::forget('cas_user');
            (new CasLoginController)->cleanUp();
            return redirect('/user')->with('status', '注册成功并已登录。');
        } catch (\Exception $e) {
            report($e);
            // return back()->withErrors(['register' => '注册时发生错误，请重试。']);
        }

    }
}