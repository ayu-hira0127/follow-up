<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PasswordController extends Controller
{
    /**
     * パスワードジェネレーター初期表示
     */
    public function index()
    {
        return view('password.generator', [
            'generatedPassword' => '',
            'length' => 8,
            'options' => [
                'lowercase' => false,
                'uppercase' => false,
                'numbers' => false,
                'symbols' => false
            ]
        ]);
    }

    /**
     * パスワード生成処理
     */
    public function generate(Request $request)
    {
        // バリデーション
        $validated = $request->validate([
            'length' => 'required|integer|min:4|max:50',
            'lowercase' => 'sometimes|boolean',
            'uppercase' => 'sometimes|boolean',
            'numbers' => 'sometimes|boolean',
            'symbols' => 'sometimes|boolean',
        ]);

        $length = $validated['length'];
        $options = [
            'lowercase' => $request->has('lowercase'),
            'uppercase' => $request->has('uppercase'),
            'numbers' => $request->has('numbers'),
            'symbols' => $request->has('symbols')
        ];

        $generatedPassword = $this->generatePassword($length, $options);

        return view('password.generator', [
            'generatedPassword' => $generatedPassword,
            'length' => $length,
            'options' => $options
        ]);
    }

    /**
     * パスワード生成ロジック
     */
    private function generatePassword($length = 8, $options = [])
    {
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';
        
        // 基本文字セット（常に英数字は使用）
        $characters = $lowercase . $uppercase . $numbers;
        $requiredChars = '';
        
        // 記号を追加
        if (isset($options['symbols']) && $options['symbols']) {
            $characters .= $symbols;
            $requiredChars .= $symbols[rand(0, strlen($symbols) - 1)];
        }
        
        // 必須文字の指定（基本セットから必ず1文字ずつ取得）
        if (isset($options['lowercase']) && $options['lowercase']) {
            $requiredChars .= $lowercase[rand(0, strlen($lowercase) - 1)];
        }
        
        if (isset($options['uppercase']) && $options['uppercase']) {
            $requiredChars .= $uppercase[rand(0, strlen($uppercase) - 1)];
        }
        
        if (isset($options['numbers']) && $options['numbers']) {
            $requiredChars .= $numbers[rand(0, strlen($numbers) - 1)];
        }
        
        // 必須文字が指定されたパスワード長を超える場合の調整
        if (strlen($requiredChars) > $length) {
            return str_shuffle(substr($requiredChars, 0, $length));
        }
        
        // 必須文字以外の残り文字数を生成
        $remainingLength = $length - strlen($requiredChars);
        $password = $requiredChars;
        
        for ($i = 0; $i < $remainingLength; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        // パスワードをシャッフル
        return str_shuffle($password);
    }
}
