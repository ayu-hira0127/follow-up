<?php

namespace App\Models;

// テストデータ生成用の機能
use Illuminate\Database\Eloquent\Factories\HasFactory;
// 認証可能なユーザーモデルの基底クラス
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * アプリケーションにログインするユーザーの情報を管理
 * パスワードはハッシュ化
 */
class User extends Authenticatable
{
    // テストデータ生成機能を使用可能にする
    use HasFactory;

    /**
     * 一括代入を許可するフィールド
     * 
     * @var list<string>
     */
    protected $fillable = [
        'name',      // ユーザー名
        'email',     // メールアドレス
        'password',  // パスワード（自動的にハッシュ化される）
    ];

    /**
     * JSONなどに変換する際に、以下のフィールドは含まれない
     * @var list<string>
     */
    protected $hidden = [
        'password',  // パスワード
    ];

    /**
     * データベースから取得した値を自動的に変換
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',  // パスワードを自動的にハッシュ化
        ];
    }
}
