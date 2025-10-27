<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('password_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // ログインユーザーのみ、匿名はnull
            $table->string('password'); // 生成されたパスワード
            $table->integer('length'); // パスワード長
            $table->json('options'); // 生成オプション（lowercase, uppercase, numbers, symbols）
            $table->string('ip_address', 45)->nullable(); // IPv6対応
            $table->string('user_agent')->nullable(); // ブラウザ情報
            $table->timestamps();
            
            // インデックス
            $table->index(['user_id', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_histories');
    }
};
