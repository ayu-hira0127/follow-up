<?php

namespace App\Models;

// LaravelのEloquentモデル（データベース操作の基本クラス）
use Illuminate\Database\Eloquent\Model;
// 暗号化・復号化機能
use Illuminate\Support\Facades\Crypt;

/**
 * ユーザーが保存したパスワード情報を管理
 * パスワードは自動的に暗号化・復号化する
 */
class SavedPassword extends Model
{
    /**
     * 一括代入を許可するフィールド
     */
    protected $fillable = [
        'user_id',    // ユーザーID
        'password',   // パスワード（自動的に暗号化される）
        'url',        // URL
        'name'        // 名前
    ];

    /**
     * データベースから取得した値を自動的に変換
     */
    protected $casts = [
        'created_at' => 'datetime',  // 作成日時をDateTime型に変換
        'updated_at' => 'datetime'   // 更新日時をDateTime型に変換
    ];

    /**
     * パスワードを取得する際に自動的に復号化
     * $password->password でアクセスしたときに自動的に呼ばれる
     */
    public function getPasswordAttribute($value)
    {
        // 空の値の場合はそのまま返す
        if (empty($value)) {
            return $value;
        }

        try {
            // 暗号化されている場合は復号化して返す
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            // 復号化に失敗した場合（既存の平文データなど）はそのまま返す
            // ログに記録して、次回保存時に暗号化される
            \Log::warning('Password decryption failed for SavedPassword ID: ' . $this->id . ' - ' . $e->getMessage());
            return $value;
        }
    }

    /**
     * パスワードを設定する際に自動的に暗号化
     * $password->password = 'xxx' で設定したときに自動的に呼ばれる
     */
    public function setPasswordAttribute($value)
    {
        // 空の値の場合はそのまま保存
        if (empty($value)) {
            $this->attributes['password'] = $value;
            return;
        }

        // 既に暗号化されているかチェック（二重暗号化を防ぐ）
        try {
            Crypt::decryptString($value);
            // 復号化できた = 既に暗号化されているのでそのまま保存
            $this->attributes['password'] = $value;
        } catch (\Exception $e) {
            // 復号化できなかった = 平文なので暗号化して保存
            $this->attributes['password'] = Crypt::encryptString($value);
        }
    }

}

