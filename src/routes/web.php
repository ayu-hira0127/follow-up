<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordController;

// Laravel初期画面
Route::get('/', function () {
    return view('welcome');
});

// パスワードジェネレーター
Route::get('/password', [PasswordController::class, 'index'])->name('password.index');
Route::post('/password', [PasswordController::class, 'generate'])->name('password.generate');
