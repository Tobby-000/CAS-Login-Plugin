# Blessing Skin CAS Single Sign-On (SSO) Integration

这个扩展包为Blessing Skin皮肤站提供了与CAS (Central Authentication Service) 单点登录系统的集成。它支持CAS v2.0协议，包含完整的认证流程、用户注册和配置管理功能。

## 功能特性

-  CAS v2.0 协议支持
-  自动用户注册系统
-  可配置的CAS服务器设置

## 安装要求

- PHP 8+
- Blessing skin 5^
- phpCAS 库 1.6^

## 安装步骤

1. 在皮肤站中安装

2.通过Composer安装包PHPCAS：
```bash
composer require apereo/phpcas^1.6
```

## 配置说明

请在插件设置页对配置进行修改

## 使用流程

### 登录流程
1. 用户访问 `/cas/login`
2. 重定向到CAS服务器进行认证
3. 认证成功后：
   - 如果用户已存在：自动登录
   - 如果用户不存在：跳转到注册页面

### 注册流程
1. 用户填写ID和密码
2. 系统自动创建账户：
   - 邮箱格式：`CAS账号 + 配置后缀`
   - 自动设置初始权限和积分
3. 注册成功后自动登录

## 注意事项

1. **CAS服务器要求**：
   - 必须支持CAS v2.0协议
   - 需要正确配置服务票据验证URL

2. **调试模式**：
   - 调试日志存储在 `storage/logs/cas.log`
   - 生产环境请关闭调试



## 许可证

本项目采用 LICENSE。