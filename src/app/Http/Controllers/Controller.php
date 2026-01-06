<?php

namespace App\Http\Controllers;

// 誰がアクセスできるかをチェック
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
// バリデーション機能
use Illuminate\Foundation\Validation\ValidatesRequests;
// Laravelの基底コントローラー
use Illuminate\Routing\Controller as BaseController;

/**
 * すべてのコントローラーの基底クラス
 * アクセスできるかのチェックとバリデーション機能を使用可能にする
 */
class Controller extends BaseController
{
    // アクセスできるかのチェックとバリデーション機能を使用可能にする
    use AuthorizesRequests, ValidatesRequests;
}