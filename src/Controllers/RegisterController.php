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

class RegisterController extends Controller
{
    public function Register(){
        return view('minejufe\cas::register');
    }
    public function handleRegister(Request $request){
        $id = $request->input('id');
        $password = $request->input('pwd');
        $user = new User();
        $user->email = $emailForDatabase;
        $user->score = option('user_initial_score');
        $user->avatar = 0;
        $user->verified = 1;
        $user->password = Hash::make($password);
        $user->ip = $request->ip();
        $user->player_name=$studentNumber;
        $user->nickname=$studentNumber;
        $user->permission = User::NORMAL;
        $user->register_at = Carbon::now();
        $user->last_sign_at = Carbon::now()->subDay();
        $user->save();
        Auth::login($user, true);
        return redirect('/user')->with('status', '注册成功并已登录。');
    }
}