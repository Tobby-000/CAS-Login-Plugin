<?php

namespace minejufe\cas;

use Option;
use App\Services\OptionForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;

class Configuration
{
    public function render()
    {
        
        $form = Option::form('cas_settings', trans('minejufe\cas::config.form_title'), function ($form) {
            
            $form
                ->text('base_path', trans('minejufe\cas::config.base_path_label'))
                ->placeholder(trans('minejufe\cas::config.base_path_placeholder'))
                ->value(Option::get('base_path', 'https://cas.example.com/cas'));
            $form
                ->text('login_uri', trans('minejufe\cas::config.login_uri_label'))
                ->placeholder(trans('minejufe\cas::config.login_uri_placeholder'))
                ->value(Option::get('login_uri', '/login'));
            $form
                ->text('logout_uri', trans('minejufe\cas::config.logout_uri_label'))
                ->placeholder(trans('minejufe\cas::config.logout_uri_placeholder'))
                ->value(Option::get('logout_uri', '/logout'));
            $form
                ->text('welcome_uri', trans('minejufe\cas::config.welcome_uri_label'))
                ->placeholder(trans('minejufe\cas::config.welcome_uri_placeholder'))
                ->value(Option::get('welcome_uri', '/index.html'));
            $form
                ->text('redirect_key', trans('minejufe\cas::config.redirect_key_label'))
                ->placeholder(trans('minejufe\cas::config.redirect_key_placeholder'))
                ->value(Option::get('redirect_key', 'service'));
            $form
                ->text('session_login_key', trans('minejufe\cas::config.session_login_key_label'))
                ->placeholder(trans('minejufe\cas::config.session_login_key_placeholder'))
                ->value(Option::get('session_login_key', 'isSupwisdomCasLogin'));
            $form
                ->text('session_user_key', trans('minejufe\cas::config.session_user_key_label'))
                ->placeholder(trans('minejufe\cas::config.session_user_key_placeholder'))
                ->value(Option::get('session_user_key', 'supwisdomCasLoginUser'));
            $form->type('info');
            // 添加国际化消息
            $form->addMessage(trans('minejufe\cas::config.info_message'), 'info');
            
            $form->before(function ($form) {
                // 预处理逻辑
            });
            
            $form->after(function ($form) {
                // 后处理逻辑
                Cache::forget('options');
                $form->type('success');
                
            });
        })->handle(function () {
            Option::set('base_path', request('base_path', 'https://cas.example.com/cas'));
            Option::set('login_uri', request('login_uri', '/login'));
            Option::set('logout_uri', request('logout_uri', '/logout'));
            Option::set('welcome_uri', request('welcome_uri', '/index.html'));
            Option::set('redirect_key', request('redirect_key', 'service'));
            Option::set('session_login_key', request('session_login_key', 'isSupwisdomCasLogin'));
            Option::set('session_user_key', request('session_user_key', 'supwisdomCasLoginUser'));
            Cache::forget('options');
            // 使用303状态码重定向，避免POST刷新问题
            return redirect()->back()
                ->with('success')
                ->setStatusCode(303);
        });
        
        
        
        return view('minejufe\cas::config', compact('form'));
    }
}