<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PasswordHistory;
use App\Models\SavedPassword;

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
     * パスワードを保存（URLと名前と一緒に）
     */
    public function save(Request $request)
    {
        // ログインチェック
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'パスワードを保存するにはログインが必要です。');
        }

        // バリデーション
        $validated = $request->validate([
            'password' => 'required|string|min:4|max:255',
            'url' => 'required|url|max:500',
            'name' => 'required|string|max:255',
        ]);

        // 保存したパスワードを保存
        SavedPassword::create([
            'user_id' => Auth::id(),
            'password' => $validated['password'],
            'url' => $validated['url'],
            'name' => $validated['name']
        ]);

        return redirect()->route('password.list')->with('success', 'パスワードを保存しました。');
    }

    /**
     * 保存したパスワード一覧を表示
     */
    public function list()
    {
        // ログインチェック
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'パスワード一覧を表示するにはログインが必要です。');
        }

        // 保存したパスワードを取得
        $savedPasswords = SavedPassword::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('password.list', [
            'savedPasswords' => $savedPasswords
        ]);
    }

    /**
     * パスワード編集フォームを表示
     */
    public function edit($id)
    {
        // ログインチェック
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'パスワードを編集するにはログインが必要です。');
        }

        // パスワードを取得（自分のもののみ）
        $password = SavedPassword::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$password) {
            return redirect()->route('password.list')->with('error', 'パスワードが見つかりません。');
        }

        return view('password.edit', [
            'password' => $password
        ]);
    }

    /**
     * パスワード情報を更新
     */
    public function update(Request $request, $id)
    {
        // ログインチェック
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'パスワードを更新するにはログインが必要です。');
        }

        // バリデーション
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:500',
            'password' => 'nullable|string|min:4|max:255',
        ]);

        // パスワードを取得（自分のもののみ）
        $password = SavedPassword::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$password) {
            return redirect()->route('password.list')->with('error', 'パスワードが見つかりません。');
        }

        // 更新
        $password->name = $validated['name'];
        $password->url = $validated['url'];
        
        // パスワードが変更された場合のみ更新
        if (!empty($validated['password'])) {
            $password->password = $validated['password'];
        }
        
        $password->save();

        return redirect()->route('password.list')->with('success', 'パスワード情報を更新しました。');
    }

    /**
     * パスワードを削除
     */
    public function destroy($id)
    {
        // ログインチェック
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'パスワードを削除するにはログインが必要です。');
        }

        // パスワードを取得（自分のもののみ）
        $password = SavedPassword::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$password) {
            return redirect()->route('password.list')->with('error', 'パスワードが見つかりません。');
        }

        // 削除
        $password->delete();

        return redirect()->route('password.list')->with('success', 'パスワードを削除しました。');
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
