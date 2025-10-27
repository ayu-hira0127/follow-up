<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
     * ユーザーとのリレーション
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 特定ユーザーの履歴を新しい順で取得
     */
    public static function getHistoryForUser($userId = null, $limit = 10)
    {
        return self::where('user_id', $userId)
                   ->orderBy('created_at', 'desc')
                   ->limit($limit)
                   ->get();
    }

    /**
     * 匿名ユーザー（セッション）の履歴取得
     */
    public static function getAnonymousHistory($sessionId, $limit = 10)
    {
        // 匿名ユーザーの場合はセッションで管理
        return session()->get('password_history', []);
    }
}
