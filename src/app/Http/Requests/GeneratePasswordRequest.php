<?php

namespace App\Http\Requests;
// Laravelのフォームリクエストクラス
use Illuminate\Foundation\Http\FormRequest;

/**
 * パスワード生成リクエストのバリデーションを処理する
 */
class GeneratePasswordRequest extends FormRequest
{
    /**
     * 誰でもパスワード生成できるようにしたいので、常にtrueを返す
     * @return bool 常にtrue
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * パスワード生成時の入力値をチェック
     * フォームから送信されたデータが、以下のルールに適合しているかを確認
     * @return array<string, string> バリデーションルールの配列
     */
    public function rules(): array
    {
        return [
            'length' => 'required|integer|min:4|max:50',  // パスワード長：必須、整数、4〜50文字
            'lowercase' => 'sometimes|boolean',            // 小文字オプション：任意、真偽値
            'uppercase' => 'sometimes|boolean',            // 大文字オプション：任意、真偽値
            'numbers' => 'sometimes|boolean',              // 数字オプション：任意、真偽値
            'symbols' => 'sometimes|boolean',             // 記号オプション：任意、真偽値
        ];
    }
}

