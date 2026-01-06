<?php

namespace App\Http\Requests;

// Laravelのフォームリクエストクラス
use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    /**
     * リクエストの認可チェック
     * ルートでauthミドルウェアを使用しているので、ここでは常にtrueを返す
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーションルール
     * パスワード更新時の入力値をチェック
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',            // 名前：必須、文字列、最大255文字
            'url' => 'required|url|max:500',                // URL：必須、URL形式、最大500文字
            'password' => 'nullable|string|min:4|max:255',  // パスワード：任意、文字列、4〜255文字(空でもOK)
        ];
    }
}

