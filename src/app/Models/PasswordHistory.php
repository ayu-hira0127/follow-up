<?php

namespace App\Models;

// LaravelのEloquentモデル（データベース操作の基本クラス）
use Illuminate\Database\Eloquent\Model;
// 暗号化・復号化機能
use Illuminate\Support\Facades\Crypt;

class PasswordHistory extends Model
{
    protected $fillable = [
        'user_id',
        'password', 
        'length',
        'options',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'options' => 'array', // JSONを配列として扱う
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * パスワードを取得する際に自動的に復号化
     */
    public function getPasswordAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }

        try {
            // 暗号化されている場合は復号化
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            // 復号化に失敗した場合（既存の平文データなど）はそのまま返す
            // ログに記録して、次回保存時に暗号化される
            \Log::warning('Password decryption failed for PasswordHistory ID: ' . $this->id . ' - ' . $e->getMessage());
            return $value;
        }
    }

    /**
     * パスワードを設定する際に自動的に暗号化
     */
    public function setPasswordAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['password'] = $value;
            return;
        }

        // 既に暗号化されているかチェック（二重暗号化を防ぐ）
        try {
            Crypt::decryptString($value);
            // 復号化できた = 既に暗号化されている
            $this->attributes['password'] = $value;
        } catch (\Exception $e) {
            // 復号化できなかった = 平文なので暗号化する
            $this->attributes['password'] = Crypt::encryptString($value);
        }
    }

}