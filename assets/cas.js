document.addEventListener('DOMContentLoaded', function() {
    // 在登录页面添加CAS登录按钮
    if (document.querySelector('.login-form')) {
        const casBtn = document.createElement('a');
        casBtn.href = '/cas/login';
        casBtn.className = 'cas-login-btn';
        casBtn.textContent = trans('cas_login.login_with_cas');
        casBtn.style.display = 'block';
        casBtn.style.marginTop = '15px';
        casBtn.style.textAlign = 'center';
        
        const loginForm = document.querySelector('.login-form');
        loginForm.parentNode.insertBefore(casBtn, loginForm.nextSibling);
    }
    
    // 在注册页面添加CAS注册按钮
    if (document.querySelector('.register-form')) {
        const casBtn = document.createElement('a');
        casBtn.href = '/cas/login';
        casBtn.className = 'cas-login-btn';
        casBtn.textContent = trans('cas_login.register_with_cas');
        casBtn.style.display = 'block';
        casBtn.style.marginTop = '15px';
        casBtn.style.textAlign = 'center';
        
        const registerForm = document.querySelector('.register-form');
        registerForm.parentNode.insertBefore(casBtn, registerForm.nextSibling);
    }
});