<?php

namespace App\Http\Controllers;

use App\Http\Requests\GeneratePasswordRequest;
use App\Http\Requests\SavePasswordRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Services\PasswordGeneratorService;
use App\Services\PasswordHistoryService;
use App\Services\SavedPasswordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PasswordController extends Controller
{
    /**
     * 各サービスが自動的に利用可能になる
     */
    public function __construct(
        private PasswordGeneratorService $passwordGenerator,  // パスワード生成サービス
        private PasswordHistoryService $passwordHistory,       // パスワード履歴サービス
        private SavedPasswordService $savedPassword            // 保存パスワードサービス
    ) {}

    /**
     * パスワードジェネレーター初期表示
     */
    public function index()
    {
        try {
            // ログイン時のみパスワード履歴を取得
            $passwordHistories = Auth::check() 
                ? $this->passwordHistory->getHistories() 
                : collect([]);
            
            // パスワードジェネレーター画面を表示
            return view('password.generator', [
                'generatedPassword' => '',      // 生成されたパスワード
                'length' => 8,                  // デフォルトのパスワードの長さ
                'options' => [                  // デフォルトのオプション
                    'lowercase' => false,
                    'uppercase' => false,
                    'numbers' => false,
                    'symbols' => false
                ],
                'passwordHistories' => $passwordHistories  // パスワード履歴
            ]);
        } catch (\Exception $e) {
            // エラーが発生した場合はログに記録
            \Log::error('PasswordController index error: ' . $e->getMessage());
            
            // エラー時も画面を表示する
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
    public function generate(GeneratePasswordRequest $request)
    {
        // バリデーション済みのデータを取得
        $validated = $request->validated();
        $length = $validated['length'];  // パスワードの長さ
        
        // チェックボックスがチェックされているかどうかを取得
        $options = [
            'lowercase' => $request->has('lowercase'),  // 小文字を含める
            'uppercase' => $request->has('uppercase'),  // 大文字を含める
            'numbers' => $request->has('numbers'),      // 数字を含める
            'symbols' => $request->has('symbols')       // 記号を含める
        ];

        // パスワードを生成
        $generatedPassword = $this->passwordGenerator->generate($length, $options);

        // ログイン時のみパスワード履歴を保存・取得
        if (Auth::check()) {
            // 生成履歴を保存
            $this->passwordHistory->save($generatedPassword, $length, $options, $request);
            // 履歴一覧を取得
            $passwordHistories = $this->passwordHistory->getHistories();
        } else {
            // 未ログイン時は空のコレクション
            $passwordHistories = collect([]);
        }

        // パスワードジェネレーター画面を表示
        return view('password.generator', [
            'generatedPassword' => $generatedPassword,  // 生成されたパスワード
            'length' => $length,                        // 使用したパスワード長
            'options' => $options,                      // 使用したオプション
            'passwordHistories' => $passwordHistories   // パスワード履歴
        ]);
    }

    /**
     * パスワードを保存（URLと名前と一緒に）
     */
    public function save(SavePasswordRequest $request)
    {
        // バリデーション済みのデータを取得
        $validated = $request->validated();
        
        // パスワードを保存
        $this->savedPassword->save(
            $validated['password'],  // パスワード
            $validated['url'],      // URL
            $validated['name']      // 名前
        );

        // パスワード一覧ページにリダイレクト
        return redirect()->route('password.list')
            ->with('success', 'パスワードを保存しました。');
    }

    /**
     * 保存したパスワード一覧を表示
     */
    public function list()
    {
        // 現在のログインユーザーの保存パスワード一覧を取得
        $savedPasswords = $this->savedPassword->getUserPasswords();

        // パスワード一覧画面を表示
        return view('password.list', [
            'savedPasswords' => $savedPasswords
        ]);
    }

    /**
     * パスワード編集フォームを表示
     */
    public function edit($id)
    {
        // 指定されたIDのパスワードを取得
        $password = $this->savedPassword->findUserPassword($id);

        // パスワードが見つからない場合は一覧ページにリダイレクト
        if (!$password) {
            return redirect()->route('password.list')
                ->with('error', 'パスワードが見つかりません。');
        }

        // パスワード編集画面を表示
        return view('password.edit', [
            'password' => $password
        ]);
    }

    /**
     * パスワード情報を更新
     */
    public function update(UpdatePasswordRequest $request, $id)
    {
        // バリデーション済みのデータを取得
        $validated = $request->validated();
        
        // 保存パスワードサービスを使ってパスワードを更新
        $password = $this->savedPassword->update($id, $validated);

        // パスワードが見つからない場合は一覧ページにリダイレクト
        if (!$password) {
            return redirect()->route('password.list')
                ->with('error', 'パスワードが見つかりません。');
        }

        // パスワード一覧ページにリダイレクト
        return redirect()->route('password.list')
            ->with('success', 'パスワード情報を更新しました。');
    }

    /**
     * パスワードを削除
     */
    public function destroy($id)
    {
        // パスワードを削除
        $deleted = $this->savedPassword->delete($id);

        // パスワードが見つからない場合は一覧ページにリダイレクト
        if (!$deleted) {
            return redirect()->route('password.list')
                ->with('error', 'パスワードが見つかりません。');
        }

        // パスワード一覧ページにリダイレクト
        return redirect()->route('password.list')
            ->with('success', 'パスワードを削除しました。');
    }
}
