<?php

namespace App\Services;

class PasswordGeneratorService
{
    /**
     * パスワードを生成
     *
     * @param int $length パスワードの長さ
     * @param array $options オプション（lowercase, uppercase, numbers, symbols）
     * @return string 生成されたパスワード
     */
    public function generate(int $length = 8, array $options = []): string
    {
        // 使用可能な文字を定義
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';  // 小文字
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';  // 大文字
        $numbers = '0123456789';                     // 数字
        $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';    // 記号
        
        // 基本文字セット（小文字、大文字、数字は常に使用可能）
        $characters = $lowercase . $uppercase . $numbers;
        $requiredChars = '';  // オプションで指定された文字種を必ず含めるための変数
        
        // 記号オプションが有効な場合
        if (isset($options['symbols']) && $options['symbols']) {
            $characters .= $symbols;  // 文字セットに記号を追加
            $requiredChars .= $symbols[rand(0, strlen($symbols) - 1)];  // 記号を1文字追加
        }
        
        // 小文字オプションが有効な場合
        if (isset($options['lowercase']) && $options['lowercase']) {
            $requiredChars .= $lowercase[rand(0, strlen($lowercase) - 1)];  // 小文字を1文字追加
        }
        
        // 大文字オプションが有効な場合
        if (isset($options['uppercase']) && $options['uppercase']) {
            $requiredChars .= $uppercase[rand(0, strlen($uppercase) - 1)];  // 大文字を1文字追加
        }
        
        // 数字オプションが有効な場合
        if (isset($options['numbers']) && $options['numbers']) {
            $requiredChars .= $numbers[rand(0, strlen($numbers) - 1)];  // 数字を1文字追加
        }
        
        // 必須文字の数が指定された長さを超える場合、必須文字のみで構成
        if (strlen($requiredChars) > $length) {
            return str_shuffle(substr($requiredChars, 0, $length));
        }
        
        // 残りの文字数を計算
        $remainingLength = $length - strlen($requiredChars);
        $password = $requiredChars;  // 必須文字から開始
        
        // 残りの文字をランダムに追加
        for ($i = 0; $i < $remainingLength; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        // 最終的にランダムに並び替えて返す
        return str_shuffle($password);
    }
}

