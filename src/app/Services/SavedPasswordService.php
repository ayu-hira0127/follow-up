<?php

namespace App\Services;

use App\Models\SavedPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class SavedPasswordService
{
    /**
     * パスワードを保存
     *
     * @param string $password パスワード
     * @param string $url URL
     * @param string $name 名前
     * @return SavedPassword
     */
    public function save(string $password, string $url, string $name): SavedPassword
    {
        // パスワードをデータベースに保存（パスワードは自動的に暗号化される）
        return SavedPassword::create([
            'user_id' => Auth::id(),    // ログインユーザーID
            'password' => $password,    // パスワード（モデルのsetPasswordAttributeで暗号化）
            'url' => $url,              // パスワードを使用するURL
            'name' => $name             // パスワードの名前（例：Gmailアカウント）
        ]);
    }

    /**
     * ユーザーの保存パスワード一覧を取得
     *
     * @param int|null $userId ユーザーID（nullの場合は現在のログインユーザー）
     * @return Collection
     */
    public function getUserPasswords(?int $userId = null): Collection
    {
        // ユーザーIDが指定されていない場合は、現在のログインユーザーIDを使用
        $userId = $userId ?? Auth::id();
        
        // 指定されたユーザーの保存パスワード一覧を新しい順で取得
        return SavedPassword::where('user_id', $userId)
            ->orderBy('created_at', 'desc')  // 作成日時の降順（新しい順）
            ->get();
    }

    /**
     * パスワードを取得（所有権チェック付き）
     *
     * @param int $id パスワードID
     * @param int|null $userId ユーザーID（nullの場合は現在のログインユーザー）
     * @return SavedPassword|null
     */
    public function findUserPassword(int $id, ?int $userId = null): ?SavedPassword
    {
        // ユーザーIDが指定されていない場合は、現在のログインユーザーIDを使用
        $userId = $userId ?? Auth::id();
        
        // 指定されたIDとユーザーIDでパスワードを検索（所有権チェック）
        // 自分のパスワードのみ取得可能
        return SavedPassword::where('id', $id)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * パスワードを更新
     *
     * @param int $id パスワードID
     * @param array $data 更新データ（name, url, password）
     * @param int|null $userId ユーザーID（nullの場合は現在のログインユーザー）
     * @return SavedPassword|null
     */
    public function update(int $id, array $data, ?int $userId = null): ?SavedPassword
    {
        // パスワードを取得（所有権チェック付き）
        $password = $this->findUserPassword($id, $userId);
        
        // パスワードが見つからない場合はnullを返す
        if (!$password) {
            return null;
        }

        // 名前とURLを更新
        $password->name = $data['name'];
        $password->url = $data['url'];
        
        // パスワードが変更された場合のみ更新（空の場合は変更しない）
        if (!empty($data['password'])) {
            $password->password = $data['password'];  // モデルのsetPasswordAttributeで自動暗号化
        }
        
        // 変更を保存
        $password->save();
        
        return $password;
    }

    /**
     * パスワードを削除
     *
     * @param int $id パスワードID
     * @param int|null $userId ユーザーID（nullの場合は現在のログインユーザー）
     * @return bool
     */
    public function delete(int $id, ?int $userId = null): bool
    {
        // パスワードを取得（所有権チェック付き）
        $password = $this->findUserPassword($id, $userId);
        
        // パスワードが見つからない場合はfalseを返す
        if (!$password) {
            return false;
        }

        // パスワードを削除
        return $password->delete();
    }
}

