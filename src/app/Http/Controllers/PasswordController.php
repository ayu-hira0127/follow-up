<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PasswordHistory;

class PasswordController extends Controller
{
    /**
     * パスワードジェネレーター初期表示
     */
    public function index()
    {
        try {
            // ログイン時のみパスワード履歴を取得
            $passwordHistories = Auth::check() ? $this->getPasswordHistories() : collect([]);
            
            return view('password.generator', [
                'generatedPassword' => '',
                'length' => 8,
                'options' => [
                    'lowercase' => false,
                    'uppercase' => false,
                    'numbers' => false,
                    'symbols' => false
                ],
                'passwordHistories' => $passwordHistories
            ]);
        } catch (\Exception $e) {
            \Log::error('PasswordController index error: ' . $e->getMessage());
            
            return view('password.generator', [
                'generatedPassword' => '',
                'length' => 8,
                'options' => [
                    'lowercase' => false,
                    'uppercase' => false,
                    'numbers' => false,
                    'symbols' => false
                ],
                'passwordHistories' => collect([])
            ]);
        }
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

        // ログイン時のみパスワード履歴を保存・取得
        if (Auth::check()) {
            $this->savePasswordHistory($generatedPassword, $length, $options, $request);
            $passwordHistories = $this->getPasswordHistories();
        } else {
            $passwordHistories = collect([]);
        }

        return view('password.generator', [
            'generatedPassword' => $generatedPassword,
            'length' => $length,
            'options' => $options,
            'passwordHistories' => $passwordHistories
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
        
        $characters = $lowercase . $uppercase . $numbers;
        $requiredChars = '';
        
        if (isset($options['symbols']) && $options['symbols']) {
            $characters .= $symbols;
            $requiredChars .= $symbols[rand(0, strlen($symbols) - 1)];
        }
        
        if (isset($options['lowercase']) && $options['lowercase']) {
            $requiredChars .= $lowercase[rand(0, strlen($lowercase) - 1)];
        }
        
        if (isset($options['uppercase']) && $options['uppercase']) {
            $requiredChars .= $uppercase[rand(0, strlen($uppercase) - 1)];
        }
        
        if (isset($options['numbers']) && $options['numbers']) {
            $requiredChars .= $numbers[rand(0, strlen($numbers) - 1)];
        }
        
        if (strlen($requiredChars) > $length) {
            return str_shuffle(substr($requiredChars, 0, $length));
        }
        
        $remainingLength = $length - strlen($requiredChars);
        $password = $requiredChars;
        
        for ($i = 0; $i < $remainingLength; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return str_shuffle($password);
    }

    /**
     * パスワード履歴を保存
     */
    private function savePasswordHistory($password, $length, $options, Request $request)
    {
        if (Auth::check()) {
            PasswordHistory::create([
                'user_id' => Auth::id(),
                'password' => $password,
                'length' => $length,
                'options' => $options,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // ユーザーの履歴数制限（最新10件のみ保持）
            $keepIds = PasswordHistory::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->pluck('id');
            
            if ($keepIds->count() > 0) {
                PasswordHistory::where('user_id', Auth::id())
                    ->whereNotIn('id', $keepIds)
                    ->delete();
            }
        } else {
            $sessionHistories = session()->get('password_history', []);
            
            array_unshift($sessionHistories, [
                'password' => $password,
                'length' => $length,
                'options' => $options,
                'created_at' => now()->toDateTimeString()
            ]);

            $sessionHistories = array_slice($sessionHistories, 0, 5);
            
            session()->put('password_history', $sessionHistories);
        }
    }

    /**
     * パスワード履歴を取得
     */
    private function getPasswordHistories()
    {
        try {
            if (Auth::check()) {
                // データベースからログインユーザーの履歴を取得
                return PasswordHistory::where('user_id', Auth::id())
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();
            } else {
                // ログインしていない場合は空のコレクションを返す
                return collect([]);
            }
        } catch (\Exception $e) {
            \Log::error('Password history retrieval failed: ' . $e->getMessage());
            return collect([]);
        }
    }
}
