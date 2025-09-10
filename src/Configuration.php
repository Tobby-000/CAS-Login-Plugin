<?php

namespace minejufe\cas\Configuration;

use Option;
use App\Services\OptionForm;

class Configuration
{
    public function render()
    {
        $form = Option::form('cas_settings', 'CAS设置', function ($form) {
            $form->group('cas_basic', '基本设置', function ($group) {
                $group->checkbox('cas_enabled', '启用CAS登录')
                    ->description('启用后，用户可以使用CAS单点登录系统进行认证');
                
                $group->checkbox('cas_auto_register', '自动注册新用户')
                    ->description('启用后，CAS认证的新用户将自动创建账户，无需填写注册表单');
            });
            
            $form->group('cas_server', 'CAS服务器配置', function ($group) {
                $group->text('cas_hostname', 'CAS服务器主机名')
                    ->placeholder('cas.example.com')
                    ->description('您的CAS服务器的主机名或IP地址');
                
                $group->text('cas_port', '端口')
                    ->placeholder('443')
                    ->description('CAS服务器的端口，通常为443');
                
                $group->text('cas_context', '上下文路径')
                    ->placeholder('/cas')
                    ->description('CAS服务器的上下文路径，通常为/cas');
                
                $group->select('cas_version', 'CAS协议版本')
                    ->option('CAS_VERSION_1_0', 'CAS 1.0')
                    ->option('CAS_VERSION_2_0', 'CAS 2.0')
                    ->option('CAS_VERSION_3_0', 'CAS 3.0')
                    ->description('根据您的CAS服务器版本选择');
            });
            
            $form->group('cas_advanced', '高级设置', function ($group) {
                $group->text('cas_certificate', 'SSL证书路径')
                    ->placeholder('/path/to/cert.pem')
                    ->description('留空则禁用SSL验证（不推荐生产环境使用）');
                
                $group->text('cas_service_base_url', '服务基础URL')
                    ->placeholder('https://your.app.com')
                    ->description('您的服务的基础URL，用于CAS服务验证');
                
                $group->text('cas_logout_redirect', '登出后重定向URL')
                    ->placeholder('https://your.app.com')
                    ->description('用户登出后重定向的URL');
            });
            
            // 添加消息
            $form->addMessage('CAS (Central Authentication Service) 是一种单点登录协议，允许用户使用同一组凭证登录多个应用。', 'info');
            
            // 添加按钮
            $form->addButton([
                'style' => 'info',
                'text' => '测试连接',
                'href' => '#',
                'class' => ['test-cas-connection']
            ]);
            
            // 表单处理前
            $form->before(function ($form) {
                // 可以在这里添加预处理逻辑
            });
            
            // 表单处理后
            $form->after(function ($form) {
                // 可以在这里添加后处理逻辑
            });
        });
        
        // 设置表单边框类型
        $form->type('primary');
        
        // 处理表单
        $form->handle();
        
        return $form->render();
    }
}