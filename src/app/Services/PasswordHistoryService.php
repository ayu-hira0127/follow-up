<?php

namespace App\Services;

use App\Models\PasswordHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class PasswordHistoryService
{
    /**
     * パスワード履歴を保存
     *
     * @param string $password 生成されたパスワード
     * @param int $length パスワードの長さ
     * @param array $options 生成オプション
     * @param Request $request リクエストオブジェクト
     * @return void
     */
    public function save(string $password, int $length, array $options, Request $request): void
    {
        // ログインしていない場合は履歴を保存しない
        if (!Auth::check()) {
            return;
        }

        // パスワード生成履歴をデータベースに保存
        PasswordHistory::create([
            'user_id' => Auth::id(),                    // ログインユーザーID
            'password' => $password,                    // 生成されたパスワード（暗号化される）
            'length' => $length,                        // パスワードの長さ
            'options' => $options,                      // 生成オプション（小文字、大文字など）
            'ip_address' => $request->ip(),             // リクエスト元のIPアドレス
            'user_agent' => $request->userAgent()       // ブラウザ情報
        ]);

        // ユーザーの履歴数制限（最新10件のみ保持、古い履歴は削除）
        $this->limitHistory(Auth::id(), 10);
    }

    /**
     * パスワード履歴を取得
     *
     * @param int|null $userId ユーザーID（nullの場合は現在のログインユーザー）
     * @param int $limit 取得件数
     * @return Collection
     */
    public function getHistories(?int $userId = null, int $limit = 10): Collection
    {
        try {
            // ユーザーIDが指定されていない場合は、現在のログインユーザーIDを使用
            $userId = $userId ?? Auth::id();
            
            // ユーザーIDが取得できない場合は空のコレクションを返す
            if (!$userId) {
                return collect([]);
            }

            // 指定されたユーザーのパスワード履歴を新しい順で取得
            return PasswordHistory::where('user_id', $userId)
                ->orderBy('created_at', 'desc')  // 作成日時の降順（新しい順）
                ->limit($limit)                  // 指定された件数まで
                ->get();
        } catch (\Exception $e) {
            // エラーが発生した場合はログに記録し、空のコレクションを返す
            \Log::error('Password history retrieval failed: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * 履歴数を制限（古い履歴を削除）
     *
     * @param int $userId ユーザーID
     * @param int $limit 保持する件数
     * @return void
     */
    private function limitHistory(int $userId, int $limit): void
    {
        // 保持する履歴のIDを取得（最新の指定件数分）
        $keepIds = PasswordHistory::where('user_id', $userId)
            ->orderBy('created_at', 'desc')  // 新しい順
            ->limit($limit)                  // 指定件数まで
            ->pluck('id');                   // IDのみを取得
        
        // 保持する履歴がある場合、それ以外の古い履歴を削除
        if ($keepIds->count() > 0) {
            PasswordHistory::where('user_id', $userId)
                ->whereNotIn('id', $keepIds)  // 保持するID以外
                ->delete();                   // 削除
        }
    }
}

