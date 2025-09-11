<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware;
use minejufe\cas\Controllers\CasLoginController;
use minejufe\cas\Controllers\RegisterController;
Route::middleware('web')->group(function () {
    Route::prefix('cas')->name('cas.')->group(function () {
        Route::middleware('guest')->group(function () {
            // CAS 登录页面
            Route::get('login', [CasLoginController::class, 'Login'])->name('login');
            Route::get('register', [RegisterController::class, 'Register'])->name('register');
            Route::post('register', [RegisterController::class, 'handleRegister'])->name('handleRegister');
        });
    });
});