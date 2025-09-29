<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware;
use minejufe\cas\Controllers\CasLoginController;
use minejufe\cas\Controllers\RegisterController;
use minejufe\cas\Controllers\RedirectController;
Route::middleware('web')->group(function () {
    Route::prefix('cas')->name('cas.')->group(function () {
        Route::middleware('guest')->group(function () {
            // CAS 登录页面
            Route::get('temp/login', [CasLoginController::class, 'Login'])->name('temp.login');
            Route::get('register', [RegisterController::class, 'Register'])->name('register');
            Route::post('register', [RegisterController::class, 'handleRegister'])->name('handleRegister');
        });
    });
});
Route::middleware('web')->group(function () {
            Route::get('cas/login', [RedirectController::class, 'Login'])->name('login');
});