<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\AuthController;

// メインページ - パスワードジェネレーター
Route::get('/', [PasswordController::class, 'index'])->name('home');

Route::post('/', [PasswordController::class, 'generate'])->name('password.generate');

// パスワード保存
Route::post('/password/save', [PasswordController::class, 'save'])->name('password.save')->middleware('auth');

// 保存したパスワード一覧
Route::get('/password/list', [PasswordController::class, 'list'])->name('password.list')->middleware('auth');

// パスワード編集
Route::get('/password/{id}/edit', [PasswordController::class, 'edit'])->name('password.edit')->middleware('auth');
Route::put('/password/{id}', [PasswordController::class, 'update'])->name('password.update')->middleware('auth');

// パスワード削除
Route::delete('/password/{id}', [PasswordController::class, 'destroy'])->name('password.destroy')->middleware('auth');

// ユーザー登録
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// ログイン
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// ログアウト
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
