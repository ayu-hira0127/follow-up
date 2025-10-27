<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\AuthController;

// メインページ - パスワードジェネレーター
Route::get('/', [PasswordController::class, 'index'])->name('home');

Route::post('/', [PasswordController::class, 'generate'])->name('password.generate');

// ユーザー登録
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// ログイン
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// ログアウト
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
