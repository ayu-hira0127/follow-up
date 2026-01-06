<?php

namespace App\Http\Controllers;

// HTTPリクエスト処理
use Illuminate\Http\Request;
// 認証機能
use Illuminate\Support\Facades\Auth;
// パスワードハッシュ化
use Illuminate\Support\Facades\Hash;
// ユーザーモデル
use App\Models\User;

/**
 * 認証関連のコントローラー
 * ユーザー登録、ログイン、ログアウトを処理
 */
class AuthController extends Controller
{

    /**
     * コンストラクタ - guestミドルウェアを適用
     * ログアウト以外のメソッドは未ログインユーザーのみアクセス可能
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    /**
     * ユーザー登録フォーム表示
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * ユーザー登録処理
     */
    public function register(Request $request)
    {
        // 入力値のバリデーション
        $request->validate([
            'name' => 'required|string|max:255',                    // 名前：必須、文字列、最大255文字
            'email' => 'required|string|email|max:255|unique:users',  // メール：必須、メール形式、最大255文字、重複不可
            'password' => 'required|string|min:8|confirmed',      // パスワード：必須、文字列、最小8文字、確認用パスワードと一致
        ]);

        // ユーザーをデータベースに作成
        $user = User::create([
            'name' => $request->name,                              // 名前
            'email' => $request->email,                            // メールアドレス
            'password' => Hash::make($request->password),         // パスワードをハッシュ化して保存
        ]);

        // 登録後、自動的にログイン状態にする
        Auth::login($user);

        // ホームページにリダイレクト
        return redirect()->route('home')->with('success', 'アカウントが作成されました！');
    }


    /**
     * ログインフォーム表示
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * ログイン処理
     */
    public function login(Request $request)
    {
        // 入力値のバリデーション
        $credentials = $request->validate([
            'email' => 'required|email',      // メール：必須、メール形式
            'password' => 'required',         // パスワード：必須
        ]);

        // 認証を試みる（メールアドレスとパスワードが一致するかチェック）
        if (Auth::attempt($credentials)) {
            // 認証成功：セッションIDを再生成
            $request->session()->regenerate();
            // ホームページにリダイレクト
            return redirect()->route('home')->with('success', 'ログインしました！');
        }

        // 認証失敗：前のページに戻り、エラーメッセージを表示
        return back()->withErrors([
            'email' => 'メールアドレスまたはパスワードが正しくありません。',
        ]);
    }

    /**
     * ログアウト処理
     */
    public function logout(Request $request)
    {
        // ユーザーをログアウト状態にする
        Auth::logout();
        
        // すべてのセッションデータを削除
        $request->session()->invalidate();
        
        // CSRFトークンを再生成（セキュリティ対策）
        $request->session()->regenerateToken();
        
        // ホームページにリダイレクト
        return redirect('/')->with('success', 'ログアウトしました！');
    }
}
