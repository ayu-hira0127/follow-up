<?php

namespace App\Http\Middleware;

// 次のコントローラーに処理を渡すためのクロージャ
use Closure;
// HTTPリクエスト処理
use Illuminate\Http\Request;
// 認証機能
use Illuminate\Support\Facades\Auth;
// HTTPレスポンス
use Symfony\Component\HttpFoundation\Response;

/**
 * 認証済みユーザーをリダイレクトするミドルウェア
 * ログイン済みのユーザーがログインページや登録ページにアクセスしようとした場合、ホームページにリダイレクトする
 */
class RedirectIfAuthenticated
{
    /**
     * 未ログインのユーザーの場合は、次の処理（ログインページ）に進む
     *
     * @param Request $request HTTPリクエスト
     * @param Closure $next 次のコントローラーに処理を渡す関数
     * @param mixed ...$guards 認証ガード(誰を、どのテーブルを使って認証するか)
     * @return Response HTTPレスポンス
     */
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        // ガードが指定されていない場合は、デフォルトのガード（null = 'web'ガード）を使用
        $guards = empty($guards) ? [null] : $guards;

        // 各ガードで認証状態をチェック
        foreach ($guards as $guard) {
            // ログイン済みかどうかをチェック
            if (Auth::guard($guard)->check()) {
                // ログイン済みの場合は、ホームページにリダイレクト
                return redirect()->route('home');
            }
        }

        // 未ログインのユーザーの場合は、次の処理（ログインページ）に進む
        return $next($request);
    }
}
