<?php

namespace App\Http\Requests;

// Laravelのフォームリクエストクラス
use Illuminate\Foundation\Http\FormRequest;

/**
 * パスワード保存リクエストのバリデーションを処理する
 */
class SavePasswordRequest extends FormRequest
{
    /**
     * ルートでauthミドルウェアを使用しているので、ここでは常にtrueを返す
     * @return bool 常にtrue
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * パスワード保存時の入力値をチェック
     * フォームから送信されたデータが、以下のルールに適合しているかを確認
     * @return array<string, string> バリデーションルールの配列
     */
    public function rules(): array
    {
        return [
            'password' => 'required|string|min:4|max:255',  // パスワード：必須、文字列、4〜255文字
            'url' => 'required|url|max:500',                // URL：必須、URL形式、最大500文字
            'name' => 'required|string|max:255',            // 名前：必須、文字列、最大255文字
        ];
    }
}

